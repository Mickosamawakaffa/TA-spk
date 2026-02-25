<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kontrakan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class BookingController extends Controller
{
    /**
     * Tampilkan daftar semua booking (admin)
     */
    public function index(Request $request)
    {
        $query = Booking::with(['kontrakan', 'user'])->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by kontrakan
        if ($request->filled('kontrakan_id')) {
            $query->where('kontrakan_id', $request->kontrakan_id);
        }

        $bookings = $query->paginate(15);
        $kontrakans = Kontrakan::orderBy('nama')->get();

        return view('admin.bookings.index', compact('bookings', 'kontrakans'));
    }

    /**
     * Tampilkan form buat booking baru
     */
    public function create(Request $request)
    {
        $kontrakans = Kontrakan::where('status', 'available')->orderBy('nama')->get();
        $selectedKontrakan = null;

        if ($request->filled('kontrakan_id')) {
            $selectedKontrakan = Kontrakan::find($request->kontrakan_id);
        }

        return view('admin.bookings.create', compact('kontrakans', 'selectedKontrakan'));
    }

    /**
     * Simpan booking baru dengan pengecekan konflik
     */
    public function store(Request $request)
    {
        $request->validate([
            'kontrakan_id' => 'required|exists:kontrakans,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'tenant_name' => 'required|string|max:255',
            'tenant_phone' => 'required|string|max:20',
            'amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ], [
            'kontrakan_id.required' => 'Pilih kontrakan terlebih dahulu.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari hari ini.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'tenant_name.required' => 'Nama penyewa wajib diisi.',
            'tenant_phone.required' => 'Nomor HP penyewa wajib diisi.',
        ]);

        try {
            // Gunakan DB transaction untuk mencegah race condition
            $booking = DB::transaction(function () use ($request) {
                // Lock kontrakan row untuk mencegah double booking
                $kontrakan = Kontrakan::lockForUpdate()->findOrFail($request->kontrakan_id);

                // Cek konflik dengan booking aktif lainnya
                $hasConflict = Booking::hasConflict(
                    $request->kontrakan_id,
                    $request->start_date,
                    $request->end_date
                );

                if ($hasConflict) {
                    throw new Exception('Kontrakan sudah dipesan untuk periode tersebut. Silakan pilih tanggal lain.');
                }

                // Buat booking
                $booking = Booking::create([
                    'kontrakan_id' => $request->kontrakan_id,
                    'user_id' => Auth::id(),
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'tenant_name' => $request->tenant_name,
                    'tenant_phone' => $request->tenant_phone,
                    'amount' => $request->amount ?? $kontrakan->harga,
                    'notes' => $request->notes,
                    'status' => Booking::STATUS_PENDING,
                    'payment_status' => Booking::PAYMENT_UNPAID,
                ]);

                return $booking;
            });

            return redirect()
                ->route('admin.bookings.show', $booking->id)
                ->with('success', 'Booking berhasil dibuat! Silakan konfirmasi booking.');

        } catch (Exception $e) {
            Log::error('Booking store error: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Simpan booking dari user (public, tanpa login)
     */
    public function userStore(Request $request)
    {
        $request->validate([
            'kontrakan_id' => 'required|exists:kontrakans,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'tenant_name' => 'required|string|max:255',
            'tenant_phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000',
        ], [
            'kontrakan_id.required' => 'Kontrakan tidak valid.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari hari ini.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'tenant_name.required' => 'Nama lengkap wajib diisi.',
            'tenant_phone.required' => 'Nomor HP wajib diisi.',
        ]);

        try {
            $booking = DB::transaction(function () use ($request) {
                $kontrakan = Kontrakan::lockForUpdate()->findOrFail($request->kontrakan_id);

                // Cek konflik
                $hasConflict = Booking::hasConflict(
                    $request->kontrakan_id,
                    $request->start_date,
                    $request->end_date
                );

                if ($hasConflict) {
                    throw new Exception('Maaf, kontrakan sudah dipesan untuk tanggal tersebut. Silakan pilih tanggal lain.');
                }

                // Hitung estimasi biaya (berdasarkan bulan)
                $start = new \DateTime($request->start_date);
                $end = new \DateTime($request->end_date);
                $diffDays = $start->diff($end)->days;
                $months = ceil($diffDays / 30);
                $amount = $months * $kontrakan->harga;

                // Buat booking dengan status pending
                return Booking::create([
                    'kontrakan_id' => $request->kontrakan_id,
                    'user_id' => Auth::id(), // null jika tidak login
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'tenant_name' => $request->tenant_name,
                    'tenant_phone' => $request->tenant_phone,
                    'amount' => $amount,
                    'notes' => $request->notes,
                    'status' => Booking::STATUS_PENDING,
                    'payment_status' => Booking::PAYMENT_UNPAID,
                ]);
            });

            // Redirect dengan pesan sukses
            return redirect()
                ->back()
                ->with('success', 'Booking berhasil dikirim! Pemilik kontrakan akan segera menghubungi Anda di nomor ' . $request->tenant_phone . '. Silakan tunggu konfirmasi.');

        } catch (Exception $e) {
            Log::error('User booking error: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Tampilkan detail booking
     */
    public function show(Booking $booking)
    {
        $booking->load(['kontrakan', 'user']);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Tampilkan form edit booking
     */
    public function edit(Booking $booking)
    {
        if (!in_array($booking->status, [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])) {
            return back()->with('error', 'Booking tidak bisa diedit karena statusnya: ' . $booking->status_label);
        }

        $kontrakans = Kontrakan::orderBy('nama')->get();
        return view('admin.bookings.edit', compact('booking', 'kontrakans'));
    }

    /**
     * Update booking
     */
    public function update(Request $request, Booking $booking)
    {
        if (!in_array($booking->status, [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])) {
            return back()->with('error', 'Booking tidak bisa diedit.');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'tenant_name' => 'required|string|max:255',
            'tenant_phone' => 'required|string|max:20',
            'amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request, $booking) {
                // Cek konflik (exclude booking ini sendiri)
                $hasConflict = Booking::hasConflict(
                    $booking->kontrakan_id,
                    $request->start_date,
                    $request->end_date,
                    $booking->id
                );

                if ($hasConflict) {
                    throw new Exception('Periode bertabrakan dengan booking lain.');
                }

                $booking->update([
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'tenant_name' => $request->tenant_name,
                    'tenant_phone' => $request->tenant_phone,
                    'amount' => $request->amount,
                    'notes' => $request->notes,
                ]);

                // Update occupied_until jika sudah checked_in
                if ($booking->status === Booking::STATUS_CHECKED_IN) {
                    $booking->kontrakan->update(['occupied_until' => $request->end_date]);
                }
            });

            return redirect()
                ->route('admin.bookings.show', $booking->id)
                ->with('success', 'Booking berhasil diupdate.');

        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Konfirmasi booking
     */
    public function confirm(Booking $booking)
    {
        if ($booking->confirm()) {
            return back()->with('success', 'Booking berhasil dikonfirmasi! Status kontrakan diubah menjadi "Dipesan".');
        }

        return back()->with('error', 'Booking tidak bisa dikonfirmasi. Status saat ini: ' . $booking->status_label);
    }

    /**
     * Check-in penyewa
     */
    public function checkIn(Booking $booking)
    {
        if ($booking->checkIn()) {
            return back()->with('success', 'Check-in berhasil! Penyewa sudah masuk. Status kontrakan diubah menjadi "Terisi".');
        }

        return back()->with('error', 'Check-in gagal. Pastikan booking sudah dikonfirmasi terlebih dahulu.');
    }

    /**
     * Check-out penyewa
     */
    public function checkOut(Booking $booking)
    {
        if ($booking->checkOut()) {
            return back()->with('success', 'Check-out berhasil! Penyewa sudah keluar. Status kontrakan kembali tersedia.');
        }

        return back()->with('error', 'Check-out gagal. Pastikan penyewa sudah check-in.');
    }

    /**
     * Batalkan booking
     */
    public function cancel(Request $request, Booking $booking)
    {
        $reason = $request->input('cancellation_reason');

        if ($booking->cancel($reason)) {
            return back()->with('success', 'Booking berhasil dibatalkan.');
        }

        return back()->with('error', 'Booking tidak bisa dibatalkan. Status saat ini: ' . $booking->status_label);
    }

    /**
     * Tandai pembayaran lunas
     */
    public function markPaid(Request $request, Booking $booking)
    {
        $method = $request->input('payment_method', 'cash');

        if ($booking->markAsPaid($method)) {
            return back()->with('success', 'Pembayaran berhasil dicatat sebagai lunas.');
        }

        return back()->with('error', 'Gagal mencatat pembayaran.');
    }

    /**
     * Toggle status pembayaran (lunas <-> belum lunas)
     */
    public function togglePaymentStatus(Request $request, Booking $booking)
    {
        if ($booking->payment_status === 'paid') {
            // Set ke belum lunas
            $booking->update([
                'payment_status' => 'unpaid',
                'paid_at' => null,
            ]);
            return back()->with('success', 'Status pembayaran diubah menjadi Belum Lunas.');
        } else {
            // Set ke lunas
            $method = $request->input('payment_method', 'cash');
            $booking->update([
                'payment_status' => 'paid',
                'payment_method' => $method,
                'paid_at' => now(),
            ]);
            return back()->with('success', 'Status pembayaran diubah menjadi Lunas.');
        }
    }

    /**
     * Hapus booking (super admin bisa hapus semua, admin biasa hanya pending/cancelled)
     */
    public function destroy(Booking $booking)
    {
        // Super admin bisa hapus booking apapun
        if (auth()->user()->role !== 'super_admin') {
            // Admin biasa hanya bisa hapus pending atau cancelled
            if (!in_array($booking->status, [Booking::STATUS_PENDING, Booking::STATUS_CANCELLED])) {
                return back()->with('error', 'Hanya booking pending atau yang dibatalkan yang bisa dihapus.');
            }
        }

        // delete() akan trigger boot()->deleted() yang auto sync status kontrakan
        $booking->delete();

        return redirect()->route('admin.bookings.index')->with('success', 'Booking berhasil dihapus.');
    }

    /**
     * API: Cek ketersediaan kontrakan
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'kontrakan_id' => 'required|exists:kontrakans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'exclude_id' => 'nullable|integer',
        ]);

        $isAvailable = Booking::isAvailable(
            $request->kontrakan_id,
            $request->start_date,
            $request->end_date,
            $request->exclude_id
        );

        // Ambil daftar booking yang bentrok (jika ada)
        $conflictingBookings = [];
        if (!$isAvailable) {
            $conflictingBookings = Booking::forKontrakan($request->kontrakan_id)
                ->active()
                ->overlapping($request->start_date, $request->end_date, $request->exclude_id)
                ->get(['id', 'start_date', 'end_date', 'tenant_name', 'status']);
        }

        return response()->json([
            'available' => $isAvailable,
            'conflicts' => $conflictingBookings,
        ]);
    }

    /**
     * Booking history untuk kontrakan tertentu
     */
    public function kontrakanHistory(Kontrakan $kontrakan)
    {
        $bookings = Booking::forKontrakan($kontrakan->id)
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('admin.bookings.kontrakan-history', compact('kontrakan', 'bookings'));
    }
}

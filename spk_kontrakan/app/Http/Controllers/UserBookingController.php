<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kontrakan;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class UserBookingController extends Controller
{
    /**
     * Tampilkan form booking untuk user
     */
    public function create(Request $request)
    {
        $kontrakan = Kontrakan::findOrFail($request->kontrakan_id);
        
        // Cek apakah kontrakan available
        if ($kontrakan->status !== 'available') {
            return redirect()->back()->with('error', 'Maaf, kontrakan ini sedang tidak tersedia.');
        }

        return view('user.booking.create', compact('kontrakan'));
    }

    /**
     * Simpan booking dari user
     */
    public function store(Request $request)
    {
        // Debug: Log all incoming data
        \Log::info('Booking store request', [
            'all_data' => $request->all(),
            'has_file' => $request->hasFile('payment_proof'),
            'file_info' => $request->file('payment_proof') ? [
                'name' => $request->file('payment_proof')->getClientOriginalName(),
                'size' => $request->file('payment_proof')->getSize(),
                'mime' => $request->file('payment_proof')->getMimeType(),
            ] : 'no file',
            'files' => $_FILES,
        ]);

        $request->validate([
            'kontrakan_id' => 'required|exists:kontrakans,id',
            'tenant_name' => 'required|string|max:255',
            'tenant_phone' => 'required|string|max:20',
            'start_date' => 'required|date|after_or_equal:today',
            'duration' => 'required|integer|min:1|max:24',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'notes' => 'nullable|string|max:1000',
        ], [
            'tenant_name.required' => 'Nama lengkap wajib diisi.',
            'tenant_phone.required' => 'Nomor HP/WhatsApp wajib diisi.',
            'start_date.required' => 'Tanggal mulai sewa wajib diisi.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari hari ini.',
            'duration.required' => 'Durasi sewa wajib diisi.',
            'duration.min' => 'Durasi sewa minimal 1 bulan.',
            'payment_proof.required' => 'Bukti pembayaran wajib diupload.',
            'payment_proof.image' => 'File harus berupa gambar.',
            'payment_proof.max' => 'Ukuran file maksimal 5MB.',
        ]);

        try {
            $kontrakan = Kontrakan::findOrFail($request->kontrakan_id);
            
            // Cast duration ke integer
            $duration = (int) $request->duration;
            
            // Hitung tanggal selesai berdasarkan durasi (bulan)
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $endDate = $startDate->copy()->addMonths($duration);
            
            // Hitung total biaya (harga per tahun / 12 * durasi bulan)
            $amount = ($kontrakan->harga / 12) * $duration;
            
            // Simpan user_id dan kontrakan_id untuk digunakan di closure
            $userId = Auth::id();
            $kontrakanId = $request->kontrakan_id;

            $booking = DB::transaction(function () use ($request, $kontrakan, $startDate, $endDate, $amount, $userId, $kontrakanId) {
                // Lock kontrakan
                $kontrakan = Kontrakan::lockForUpdate()->findOrFail($kontrakanId);

                // Cek konflik
                $hasConflict = Booking::hasConflict(
                    $kontrakanId,
                    $startDate->format('Y-m-d'),
                    $endDate->format('Y-m-d')
                );

                if ($hasConflict) {
                    throw new Exception('Maaf, kontrakan sudah dipesan untuk periode tersebut. Silakan pilih tanggal lain.');
                }

                // Upload bukti pembayaran
                $paymentProofPath = null;
                if ($request->hasFile('payment_proof')) {
                    $file = $request->file('payment_proof');
                    $filename = 'payment_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $paymentProofPath = $file->storeAs('payment_proofs', $filename, 'public');
                }

                // Buat booking
                $booking = Booking::create([
                    'kontrakan_id' => $kontrakanId,
                    'user_id' => $userId,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'tenant_name' => $request->tenant_name,
                    'tenant_phone' => $request->tenant_phone,
                    'amount' => $amount,
                    'payment_proof' => $paymentProofPath,
                    'payment_status' => Booking::PAYMENT_UNPAID,
                    'status' => Booking::STATUS_PENDING,
                    'booking_source' => 'user',
                    'notes' => $request->notes,
                ]);

                // Hapus dari favorit setelah booking berhasil
                Favorite::where('user_id', $userId)
                    ->where('type', 'kontrakan')
                    ->where('item_id', $kontrakanId)
                    ->delete();

                return $booking;
            });

            return redirect()->route('user.booking.success', $booking->id)
                ->with('success', 'Booking berhasil dikirim! Mohon tunggu konfirmasi dari pemilik kontrakan.');

        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Halaman sukses booking
     */
    public function success($id)
    {
        $booking = Booking::with('kontrakan')->findOrFail($id);
        
        // Pastikan booking milik user yang login atau baru dibuat
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        return view('user.booking.success', compact('booking'));
    }

    /**
     * Riwayat booking user
     */
    public function history()
    {
        $bookings = Booking::with('kontrakan')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.booking.history', compact('bookings'));
    }

    /**
     * Detail booking user
     */
    public function show($id)
    {
        $booking = Booking::with('kontrakan')->findOrFail($id);
        
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        return view('user.booking.show', compact('booking'));
    }

    /**
     * Cancel booking oleh user (hanya jika masih pending)
     */
    public function cancel(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->status !== Booking::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Booking tidak dapat dibatalkan.');
        }

        $booking->update([
            'status' => Booking::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => 'Dibatalkan oleh penyewa',
        ]);

        return redirect()->route('user.booking.history')
            ->with('success', 'Booking berhasil dibatalkan.');
    }

    /**
     * Form perpanjang kontrak
     */
    public function extend($id)
    {
        $booking = Booking::with('kontrakan')->findOrFail($id);
        
        // Pastikan booking milik user yang login
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Hanya booking yang completed atau checked_in yang bisa diperpanjang
        if (!in_array($booking->status, [Booking::STATUS_COMPLETED, Booking::STATUS_CHECKED_IN, Booking::STATUS_CONFIRMED])) {
            return redirect()->back()->with('error', 'Booking ini tidak dapat diperpanjang.');
        }

        // Cek apakah kontrakan masih ada
        if (!$booking->kontrakan) {
            return redirect()->back()->with('error', 'Kontrakan tidak ditemukan.');
        }

        return view('user.booking.extend', compact('booking'));
    }

    /**
     * Simpan perpanjangan kontrak
     */
    public function storeExtension(Request $request, $id)
    {
        $originalBooking = Booking::with('kontrakan')->findOrFail($id);
        
        // Pastikan booking milik user yang login
        if ($originalBooking->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'duration' => 'required|integer|min:1|max:24',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'notes' => 'nullable|string|max:1000',
        ], [
            'duration.required' => 'Durasi perpanjangan wajib diisi.',
            'duration.min' => 'Durasi minimal 1 bulan.',
            'payment_proof.required' => 'Bukti pembayaran wajib diupload.',
            'payment_proof.image' => 'File harus berupa gambar.',
            'payment_proof.max' => 'Ukuran file maksimal 5MB.',
        ]);

        try {
            $kontrakan = $originalBooking->kontrakan;
            $duration = (int) $request->duration;
            
            // Tanggal mulai = tanggal selesai booking sebelumnya
            $startDate = $originalBooking->end_date->copy();
            
            // Jika tanggal selesai sudah lewat, mulai dari hari ini
            if ($startDate->isPast()) {
                $startDate = \Carbon\Carbon::today();
            }
            
            $endDate = $startDate->copy()->addMonths($duration);
            
            // Hitung total biaya
            $amount = ($kontrakan->harga / 12) * $duration;

            $booking = DB::transaction(function () use ($request, $originalBooking, $kontrakan, $startDate, $endDate, $amount) {
                // Cek konflik jadwal
                $hasConflict = Booking::hasConflict(
                    $kontrakan->id,
                    $startDate->format('Y-m-d'),
                    $endDate->format('Y-m-d'),
                    $originalBooking->id // exclude original booking
                );

                if ($hasConflict) {
                    throw new Exception('Maaf, kontrakan sudah dipesan untuk periode tersebut.');
                }

                // Upload bukti pembayaran
                $paymentProofPath = null;
                if ($request->hasFile('payment_proof')) {
                    $file = $request->file('payment_proof');
                    $filename = 'payment_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $paymentProofPath = $file->storeAs('payment_proofs', $filename, 'public');
                }

                // Buat booking baru sebagai perpanjangan
                $booking = Booking::create([
                    'kontrakan_id' => $kontrakan->id,
                    'user_id' => Auth::id(),
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'tenant_name' => $originalBooking->tenant_name,
                    'tenant_phone' => $originalBooking->tenant_phone,
                    'amount' => $amount,
                    'payment_proof' => $paymentProofPath,
                    'payment_status' => Booking::PAYMENT_UNPAID,
                    'status' => Booking::STATUS_PENDING,
                    'booking_source' => 'user',
                    'notes' => $request->notes ? "Perpanjangan dari Booking #{$originalBooking->id}. " . $request->notes : "Perpanjangan dari Booking #{$originalBooking->id}",
                ]);

                return $booking;
            });

            return redirect()->route('user.booking.success', $booking->id)
                ->with('success', 'Perpanjangan kontrak berhasil diajukan! Menunggu konfirmasi admin.');

        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}

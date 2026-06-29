<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Kontrakan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    private const PAYMENT_PROOF_DIR = 'payment_proofs';
    private const PAYMENT_PROOF_PRIVATE_DISK = 'private';
    private const PAYMENT_PROOF_PUBLIC_DISK = 'public';

    /**
     * List bookings user (history)
     */
    public function index(Request $request)
    {
        $bookings = Booking::with('kontrakan')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $bookings
        ], 200);
    }

    /**
     * Show booking detail
     */
    public function show(Request $request, $id)
    {
        $booking = Booking::with('kontrakan', 'user')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan',
                'error_code' => 'NOT_FOUND',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $booking
        ], 200);
    }

    /**
     * Create pengajuan baru (survei atau sewa) dari mobile
     */
    public function store(Request $request)
    {
        $jenis = $request->input('jenis_pengajuan', 'sewa');

        if ($jenis === 'survei') {
            return $this->storeSurvei($request);
        }

        return $this->storeSewa($request);
    }

    /**
     * Simpan pengajuan survei
     */
    private function storeSurvei(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kontrakan_id'   => 'required|exists:kontrakans,id',
            'tanggal_survei' => 'required|date|after:today',
            'jam_survei'     => 'required|string',
            'catatan'        => 'nullable|string|max:1000',
        ], [
            'kontrakan_id.required'   => 'Kontrakan wajib dipilih',
            'tanggal_survei.required' => 'Tanggal survei wajib diisi',
            'tanggal_survei.after'    => 'Tanggal survei harus setelah hari ini',
            'jam_survei.required'     => 'Jam survei wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'    => false,
                'message'    => 'Validasi gagal',
                'error_code' => 'VALIDATION_ERROR',
                'errors'     => $validator->errors(),
            ], 422);
        }

        $kontrakan = Kontrakan::find($request->kontrakan_id);

        if (!$kontrakan) {
            return response()->json([
                'success'    => false,
                'message'    => 'Kontrakan tidak ditemukan',
                'error_code' => 'NOT_FOUND',
            ], 404);
        }

        // Untuk survei, kontrakan cukup tersedia (tidak occupied)
        if ($kontrakan->status === 'occupied') {
            return response()->json([
                'success'    => false,
                'message'    => 'Kontrakan sedang ditempati',
                'error_code' => 'NOT_AVAILABLE',
            ], 400);
        }

        $tanggalSurvei = Carbon::parse($request->tanggal_survei);

        $booking = Booking::create([
            'user_id'          => $request->user()->id,
            'kontrakan_id'     => $request->kontrakan_id,
            'jenis_pengajuan'  => 'survei',
            'tanggal_survei'   => $tanggalSurvei,
            'jam_survei'       => $request->jam_survei,
            'start_date'       => $tanggalSurvei,
            'end_date'         => $tanggalSurvei,
            'amount'           => 0,
            'status'           => 'pending',
            'payment_status'   => 'unpaid',
            'booking_source'   => 'user',
            'notes'            => $request->catatan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan survei berhasil dikirim. Tunggu konfirmasi dari pemilik.',
            'data'    => $booking->load('kontrakan'),
        ], 201);
    }

    /**
     * Simpan pengajuan sewa
     */
    private function storeSewa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kontrakan_id' => 'required|exists:kontrakans,id',
            'tanggal_mulai' => 'required|date|after:today',
            'durasi_bulan' => 'required|integer|min:1|max:12',
            'catatan'      => 'nullable|string|max:1000',
            'ktp_photo'    => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
        ], [
            'kontrakan_id.required'  => 'Kontrakan wajib dipilih',
            'tanggal_mulai.required' => 'Tanggal mulai sewa wajib diisi',
            'tanggal_mulai.after'    => 'Tanggal mulai harus setelah hari ini',
            'durasi_bulan.required'  => 'Durasi sewa wajib diisi',
            'ktp_photo.image'        => 'Foto KTP harus berupa gambar',
            'ktp_photo.mimes'        => 'Format KTP harus jpeg, jpg, atau png',
            'ktp_photo.max'          => 'Ukuran foto KTP maksimal 5MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'    => false,
                'message'    => 'Validasi gagal',
                'error_code' => 'VALIDATION_ERROR',
                'errors'     => $validator->errors(),
            ], 422);
        }

        $kontrakan = Kontrakan::find($request->kontrakan_id);

        if (!$kontrakan) {
            return response()->json([
                'success'    => false,
                'message'    => 'Kontrakan tidak ditemukan',
                'error_code' => 'NOT_FOUND',
            ], 404);
        }

        // Cek ketersediaan kontrakan
        if (!in_array($kontrakan->status, ['tersedia', 'available'])) {
            return response()->json([
                'success'    => false,
                'message'    => 'Kontrakan tidak tersedia untuk disewa',
                'error_code' => 'NOT_AVAILABLE',
            ], 400);
        }

        $startDate = Carbon::parse($request->tanggal_mulai);
        $endDate   = $startDate->copy()->addMonths((int)$request->durasi_bulan);
        $amount    = $kontrakan->harga * ((int)$request->durasi_bulan / 12);

        $bookingData = [
            'user_id'         => $request->user()->id,
            'kontrakan_id'    => $request->kontrakan_id,
            'jenis_pengajuan' => 'sewa',
            'start_date'      => $startDate,
            'end_date'        => $endDate,
            'amount'          => $amount,
            'status'          => 'pending',
            'payment_status'  => 'unpaid',
            'booking_source'  => 'user',
            'notes'           => $request->catatan,
        ];

        // Upload foto KTP jika disertakan
        if ($request->hasFile('ktp_photo')) {
            $ktpPath = $request->file('ktp_photo')->store('ktp_photos', self::PAYMENT_PROOF_PRIVATE_DISK);
            $bookingData['ktp_photo'] = $ktpPath;
        }

        $booking = Booking::create($bookingData);

        // Update status kontrakan ke booked
        $kontrakan->update(['status' => 'booked']);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan sewa berhasil dikirim. Tunggu persetujuan pemilik.',
            'data'    => $booking->load('kontrakan'),
        ], 201);
    }

    /**
     * Cancel booking
     */
    public function cancel(Request $request, $id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan',
                'error_code' => 'NOT_FOUND',
            ], 404);
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak dapat dibatalkan',
                'error_code' => 'INVALID_STATUS',
            ], 400);
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Sync status kontrakan otomatis
        Booking::syncKontrakanStatus($booking->kontrakan_id);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibatalkan',
            'data' => $booking->fresh()->load('kontrakan')
        ], 200);
    }

    /**
     * Extend booking (perpanjangan sewa)
     */
    public function extend(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'durasi_bulan' => 'required|integer|min:1|max:12',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'error_code' => 'VALIDATION_ERROR',
                'errors' => $validator->errors()
            ], 422);
        }

        $booking = Booking::with('kontrakan')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan',
                'error_code' => 'NOT_FOUND',
            ], 404);
        }

        if ($booking->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya booking aktif yang dapat diperpanjang',
                'error_code' => 'INVALID_STATUS',
            ], 400);;
        }

        // Create new booking untuk perpanjangan
        $startDate = Carbon::parse($booking->end_date);
        $endDate = $startDate->copy()->addMonths($request->durasi_bulan);
        $amount = $booking->kontrakan->harga * ($request->durasi_bulan / 12);

        $newBooking = Booking::create([
            'user_id' => $request->user()->id,
            'kontrakan_id' => $booking->kontrakan_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'amount' => $amount,
            'status' => 'pending',
            'notes' => 'Perpanjangan dari booking #' . $booking->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perpanjangan booking berhasil dibuat',
            'data' => $newBooking->load('kontrakan')
        ], 201);
    }

    /**
     * Upload bukti pembayaran dari mobile
     */
    public function uploadPaymentProof(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'payment_proof' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ], [
            'payment_proof.required' => 'File bukti pembayaran wajib diunggah',
            'payment_proof.image'    => 'File harus berupa gambar',
            'payment_proof.mimes'    => 'Format file harus jpeg, jpg, atau png',
            'payment_proof.max'      => 'Ukuran file maksimal 5MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $booking = Booking::with('kontrakan')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan',
                'error_code' => 'NOT_FOUND',
            ], 404);
        }

        if (in_array($booking->status, ['cancelled', 'completed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Booking ini sudah tidak aktif',
                'error_code' => 'INVALID_STATUS',
            ], 400);
        }

        if (in_array($booking->payment_status, ['paid', 'verification'])) {
            return response()->json([
                'success'    => false,
                'message'    => $booking->payment_status === 'paid'
                    ? 'Pembayaran booking ini sudah dikonfirmasi'
                    : 'Bukti pembayaran sudah diunggah dan sedang menunggu verifikasi admin',
                'error_code' => 'ALREADY_PAID',
            ], 400);
        }

        // Hapus bukti lama jika ada
        if ($booking->payment_proof) {
            // ✅ Delete from both disks to support migration from old public storage
            Storage::disk(self::PAYMENT_PROOF_PUBLIC_DISK)->delete($booking->payment_proof);
            Storage::disk(self::PAYMENT_PROOF_PRIVATE_DISK)->delete($booking->payment_proof);
        }

        // Simpan gambar baru
        // ✅ Store sensitive payment proof in PRIVATE storage
        $path = $request->file('payment_proof')->store(self::PAYMENT_PROOF_DIR, self::PAYMENT_PROOF_PRIVATE_DISK);

        // Update booking – status jadi 'verification' agar admin bisa memverifikasi
        $booking->update([
            'payment_proof'  => $path,
            'payment_status' => 'verification',
            'payment_method' => 'transfer',
            'payment_rejection_reason' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.',
            'data'    => $booking->fresh()->load('kontrakan'),
        ], 200);
    }

    /**
     * Securely stream payment proof file (AUTH required).
     * - Only booking owner can access.
     * - Migrates legacy public-stored files to private storage on first access.
     */
    public function getPaymentProof(Request $request, $id)
    {
        $booking = Booking::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan',
                'error_code' => 'NOT_FOUND',
            ], 404);
        }

        if (!$booking->payment_proof) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti pembayaran tidak tersedia',
                'error_code' => 'NO_PAYMENT_PROOF',
            ], 404);
        }

        $path = $booking->payment_proof;

        // If file is still on public disk (legacy), migrate to private.
        if (!Storage::disk(self::PAYMENT_PROOF_PRIVATE_DISK)->exists($path) && Storage::disk(self::PAYMENT_PROOF_PUBLIC_DISK)->exists($path)) {
            try {
                $readStream = Storage::disk(self::PAYMENT_PROOF_PUBLIC_DISK)->readStream($path);
                if ($readStream !== false) {
                    Storage::disk(self::PAYMENT_PROOF_PRIVATE_DISK)->writeStream($path, $readStream);
                    if (is_resource($readStream)) {
                        fclose($readStream);
                    }
                    Storage::disk(self::PAYMENT_PROOF_PUBLIC_DISK)->delete($path);
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to migrate payment proof to private storage', [
                    'booking_id' => $booking->id,
                    'path' => $path,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (!Storage::disk(self::PAYMENT_PROOF_PRIVATE_DISK)->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File bukti pembayaran tidak ditemukan',
                'error_code' => 'FILE_NOT_FOUND',
            ], 404);
        }

        $absolutePath = Storage::disk(self::PAYMENT_PROOF_PRIVATE_DISK)->path($path);

        // Stream file. Browser/app will infer mime.
        return response()->file($absolutePath, [
            'Content-Disposition' => 'inline; filename="payment-proof-' . $booking->id . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }
}

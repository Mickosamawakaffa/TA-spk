<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Kontrakan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
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
     * Create booking baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kontrakan_id' => 'required|exists:kontrakans,id',
            'tanggal_mulai' => 'required|date|after:today',
            'durasi_bulan' => 'required|integer|min:1|max:12',
            'catatan' => 'nullable|string',
            'payment_proof' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ], [
            'payment_proof.required' => 'Bukti pembayaran wajib diunggah',
            'payment_proof.image' => 'File harus berupa gambar',
            'payment_proof.mimes' => 'Format file harus jpeg, jpg, atau png',
            'payment_proof.max' => 'Ukuran file maksimal 5MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'error_code' => 'VALIDATION_ERROR',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check kontrakan availability
        $kontrakan = Kontrakan::find($request->kontrakan_id);
        
        // Handle both status values: 'tersedia' and 'available'
        if (!in_array($kontrakan->status, ['tersedia', 'available'])) {
            return response()->json([
                'success' => false,
                'message' => 'Kontrakan tidak tersedia',
                'error_code' => 'NOT_AVAILABLE',
            ], 400);
        }

        // Calculate tanggal selesai
        $startDate = Carbon::parse($request->tanggal_mulai);
        $endDate = $startDate->copy()->addMonths((int)$request->durasi_bulan);

        // Calculate total biaya
        $amount = $kontrakan->harga * (int)$request->durasi_bulan;

        $bookingData = [
            'user_id' => $request->user()->id,
            'kontrakan_id' => $request->kontrakan_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'amount' => $amount,
            'status' => 'pending',
            'notes' => $request->catatan,
        ];

        // Handle payment proof upload
        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');
            $bookingData['payment_proof'] = $path;
            $bookingData['payment_status'] = 'paid';
            $bookingData['payment_method'] = 'transfer';
            $bookingData['paid_at'] = now();
        }

        $booking = Booking::create($bookingData);

        // Update status kontrakan ke booked saat ada booking masuk
        $kontrakan->update(['status' => 'booked']);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibuat',
            'data' => $booking->load('kontrakan')
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
        $amount = $booking->kontrakan->harga * $request->durasi_bulan;

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

        if ($booking->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran booking ini sudah dikonfirmasi',
                'error_code' => 'ALREADY_PAID',
            ], 400);
        }

        // Hapus bukti lama jika ada
        if ($booking->payment_proof) {
            Storage::disk('public')->delete($booking->payment_proof);
        }

        // Simpan gambar baru
        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        // Update booking
        $booking->update([
            'payment_proof'  => $path,
            'payment_status' => 'paid',
            'payment_method' => 'transfer',
            'paid_at'        => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran berhasil diunggah. Pembayaran Anda telah dikonfirmasi.',
            'data'    => $booking->fresh()->load('kontrakan'),
        ], 200);
    }
}

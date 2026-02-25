<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Kontrakan;
use App\Models\Laundry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Store review untuk kontrakan
     */
    public function storeKontrakan(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $kontrakan = Kontrakan::find($id);
        
        if (!$kontrakan) {
            return response()->json([
                'success' => false,
                'message' => 'Kontrakan tidak ditemukan',
                'error_code' => 'NOT_FOUND',
            ], 404);
        }

        // Check if user already reviewed
        $existingReview = Review::where('user_id', $request->user()->id)
            ->where('reviewable_type', Kontrakan::class)
            ->where('reviewable_id', $id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memberikan review untuk kontrakan ini',
                'error_code' => 'ALREADY_REVIEWED',
            ], 400);
        }

        $review = Review::create([
            'user_id' => $request->user()->id,
            'reviewable_type' => Kontrakan::class,
            'reviewable_id' => $id,
            'rating' => $request->rating,
            'komentar' => $request->komentar,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review berhasil ditambahkan',
            'data' => $review->load('user:id,name,email')
        ], 201);
    }

    /**
     * Store review untuk laundry
     */
    public function storeLaundry(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $laundry = Laundry::find($id);
        
        if (!$laundry) {
            return response()->json([
                'success' => false,
                'message' => 'Laundry tidak ditemukan',
                'error_code' => 'NOT_FOUND',
            ], 404);
        }

        // Check if user already reviewed
        $existingReview = Review::where('user_id', $request->user()->id)
            ->where('reviewable_type', Laundry::class)
            ->where('reviewable_id', $id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memberikan review untuk laundry ini',
                'error_code' => 'ALREADY_REVIEWED',
            ], 400);
        }

        $review = Review::create([
            'user_id' => $request->user()->id,
            'reviewable_type' => Laundry::class,
            'reviewable_id' => $id,
            'rating' => $request->rating,
            'komentar' => $request->komentar,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review berhasil ditambahkan',
            'data' => $review->load('user:id,name,email')
        ], 201);
    }

    /**
     * Update review
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $review = Review::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review tidak ditemukan',
                'error_code' => 'NOT_FOUND',
            ], 404);
        }

        $review->update([
            'rating' => $request->rating,
            'komentar' => $request->komentar,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review berhasil diupdate',
            'data' => $review->load('user:id,name,email')
        ], 200);
    }

    /**
     * Delete review
     */
    public function destroy(Request $request, $id)
    {
        $review = Review::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review tidak ditemukan',
                'error_code' => 'NOT_FOUND',
            ], 404);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review berhasil dihapus'
        ], 200);
    }
}

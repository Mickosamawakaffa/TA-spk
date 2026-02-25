<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Kontrakan;
use App\Models\Laundry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Tampilkan form review (opsional, bisa langsung di view detail)
     */
    public function create($type, $id)
    {
        $item = $this->getItem($type, $id);
        
        if (!$item) {
            return back()->with('error', ucfirst($type) . ' tidak ditemukan');
        }

        return view('reviews.create', compact('item', 'type'));
    }

    /**
     * Store review untuk kontrakan
     */
    public function storeKontrakan(Request $request, Kontrakan $kontrakan)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000'
        ]);

        // Cek apakah user sudah pernah review
        $existingReview = Review::where('type', 'kontrakan')
                               ->where('item_id', $kontrakan->id)
                               ->where('user_id', Auth::id())
                               ->first();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan review untuk kontrakan ini');
        }

        Review::create([
            'type' => 'kontrakan',
            'item_id' => $kontrakan->id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'review' => $request->review
        ]);

        return back()->with('success', 'Review berhasil ditambahkan');
    }

    /**
     * Store review untuk laundry
     */
    public function storeLaundry(Request $request, Laundry $laundry)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000'
        ]);

        // Cek apakah user sudah pernah review
        $existingReview = Review::where('type', 'laundry')
                               ->where('item_id', $laundry->id)
                               ->where('user_id', Auth::id())
                               ->first();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan review untuk laundry ini');
        }

        Review::create([
            'type' => 'laundry',
            'item_id' => $laundry->id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'review' => $request->review
        ]);

        return back()->with('success', 'Review berhasil ditambahkan');
    }

    /**
     * Simpan review baru (legacy)
     */
    public function store(Request $request, $type, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000'
        ]);

        // Cek apakah user sudah pernah review item ini
        $existingReview = Review::where('type', $type)
                               ->where('item_id', $id)
                               ->where('user_id', Auth::id())
                               ->first();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan review untuk ' . $type . ' ini');
        }

        Review::create([
            'type' => $type,
            'item_id' => $id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'review' => $request->review
        ]);

        return back()->with('success', 'Review berhasil ditambahkan');
    }

    /**
     * Update review
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000'
        ]);

        $review = Review::findOrFail($id);

        // Cek apakah user adalah pemilik review
        if ($review->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengedit review ini');
        }

        $review->update([
            'rating' => $request->rating,
            'review' => $request->review
        ]);

        return back()->with('success', 'Review berhasil diupdate');
    }

    /**
     * Hapus review
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        // Cek apakah user adalah pemilik review atau admin
        if ($review->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus review ini');
        }

        $review->delete();

        return back()->with('success', 'Review berhasil dihapus');
    }

    /**
     * Helper untuk get item (Kontrakan atau Laundry)
     */
    private function getItem($type, $id)
    {
        if ($type === 'kontrakan') {
            return Kontrakan::find($id);
        } elseif ($type === 'laundry') {
            return Laundry::find($id);
        }
        return null;
    }
}
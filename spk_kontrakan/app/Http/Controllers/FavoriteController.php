<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Kontrakan;
use App\Models\Laundry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Toggle favorite untuk kontrakan
     */
    public function toggleKontrakan(Request $request, Kontrakan $kontrakan)
    {
        return $this->toggle($request, 'kontrakan', $kontrakan->id);
    }

    /**
     * Toggle favorite untuk laundry
     */
    public function toggleLaundry(Request $request, Laundry $laundry)
    {
        return $this->toggle($request, 'laundry', $laundry->id);
    }

    /**
     * Toggle favorite (tambah/hapus)
     * Bisa dipanggil via AJAX untuk UX yang smooth
     */
    public function toggle(Request $request, $type, $id)
    {
        $userId = Auth::id();

        // Cek apakah sudah difavoritkan
        $favorite = Favorite::where('type', $type)
                           ->where('item_id', $id)
                           ->where('user_id', $userId)
                           ->first();

        if ($favorite) {
            // Jika sudah ada, hapus (unfavorite)
            $favorite->delete();
            $status = 'removed';
            $message = $type === 'kontrakan' ? '❌ Kontrakan dihapus dari favorit' : '❌ Laundry dihapus dari favorit';
        } else {
            // Jika belum ada, tambah (favorite)
            Favorite::create([
                'type' => $type,
                'item_id' => $id,
                'user_id' => $userId
            ]);
            $status = 'added';
            $message = $type === 'kontrakan' ? '❤️ Kontrakan berhasil ditambahkan ke favorit!' : '❤️ Laundry berhasil ditambahkan ke favorit!';
        }

        // Hitung total favorites untuk item ini
        $totalFavorites = Favorite::where('type', $type)
                                 ->where('item_id', $id)
                                 ->count();

        // Jika request AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $status,
                'message' => $message,
                'total_favorites' => $totalFavorites
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Tampilkan list favorites user
     */
    public function index(Request $request)
    {
        $type = $request->get('type'); // 'kontrakan' atau 'laundry' atau null (semua)

        $query = Favorite::where('user_id', Auth::id())
                        ->with(['kontrakan', 'laundry'])
                        ->latest();

        if ($type) {
            $query->where('type', $type);
        }

        $favorites = $query->paginate(12);

        return view('favorites.index', compact('favorites', 'type'));
    }

    /**
     * Hapus dari favorites
     */
    public function destroy($id)
    {
        $favorite = Favorite::findOrFail($id);

        // Cek apakah user adalah pemilik favorite
        if ($favorite->user_id !== Auth::id()) {
            return back()->with('error', 'Akses ditolak');
        }

        $favorite->delete();

        return back()->with('success', 'Berhasil dihapus dari favorit');
    }

    /**
     * Cek apakah item sudah difavoritkan (untuk AJAX check)
     */
    public function check($type, $id)
    {
        $isFavorited = Favorite::where('type', $type)
                              ->where('item_id', $id)
                              ->where('user_id', Auth::id())
                              ->exists();

        return response()->json([
            'is_favorited' => $isFavorited
        ]);
    }
}
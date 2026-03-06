<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Kontrakan;
use App\Models\Laundry;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * List favorites user — separated by type
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $kontrakanIds = Favorite::where('user_id', $userId)
            ->where('type', 'kontrakan')
            ->pluck('item_id')
            ->toArray();

        $laundryIds = Favorite::where('user_id', $userId)
            ->where('type', 'laundry')
            ->pluck('item_id')
            ->toArray();

        $kontrakanList = Kontrakan::whereIn('id', $kontrakanIds)->get();
        $laundryList   = Laundry::whereIn('id', $laundryIds)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'kontrakan' => $kontrakanList,
                'laundry'   => $laundryList,
            ],
        ], 200);
    }

    /**
     * Toggle favorite kontrakan
     */
    public function toggleKontrakan(Request $request, $id)
    {
        $kontrakan = Kontrakan::find($id);
        
        if (!$kontrakan) {
            return response()->json([
                'success' => false,
                'message' => 'Kontrakan tidak ditemukan'
            ], 404);
        }

        $favorite = Favorite::where('user_id', $request->user()->id)
            ->where('type', 'kontrakan')
            ->where('item_id', $id)
            ->first();

        if ($favorite) {
            // Remove from favorites
            $favorite->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Kontrakan dihapus dari favorit',
                'is_favorited' => false
            ], 200);
        } else {
            // Add to favorites
            Favorite::create([
                'user_id' => $request->user()->id,
                'type'    => 'kontrakan',
                'item_id' => $id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kontrakan ditambahkan ke favorit',
                'is_favorited' => true,
            ], 201);
        }
    }

    /**
     * Toggle favorite laundry
     */
    public function toggleLaundry(Request $request, $id)
    {
        $laundry = Laundry::find($id);
        
        if (!$laundry) {
            return response()->json([
                'success' => false,
                'message' => 'Laundry tidak ditemukan'
            ], 404);
        }

        $favorite = Favorite::where('user_id', $request->user()->id)
            ->where('type', 'laundry')
            ->where('item_id', $id)
            ->first();

        if ($favorite) {
            // Remove from favorites
            $favorite->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Laundry dihapus dari favorit',
                'is_favorited' => false
            ], 200);
        } else {
            // Add to favorites
            Favorite::create([
                'user_id' => $request->user()->id,
                'type'    => 'laundry',
                'item_id' => $id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Laundry ditambahkan ke favorit',
                'is_favorited' => true,
            ], 201);
        }
    }

    /**
     * Delete favorite by ID
     */
    public function destroy(Request $request, $id)
    {
        $favorite = Favorite::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$favorite) {
            return response()->json([
                'success' => false,
                'message' => 'Favorite tidak ditemukan'
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Favorite berhasil dihapus'
        ], 200);
    }
}

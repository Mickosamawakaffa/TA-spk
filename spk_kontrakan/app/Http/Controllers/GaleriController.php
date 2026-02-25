<?php

namespace App\Http\Controllers;

use App\Models\Galeri;
use App\Models\Kontrakan;
use App\Models\Laundry;
use Illuminate\Http\Request;

class GaleriController extends Controller
{
    /**
     * Upload foto untuk kontrakan
     */
    public function uploadKontrakan(Request $request, Kontrakan $kontrakan)
    {
        $request->validate([
            'fotos' => 'required',
            'fotos.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $uploadedCount = 0;

        if ($request->hasFile('fotos')) {
            // Ambil urutan terakhir
            $lastUrutan = Galeri::where('type', 'kontrakan')
                               ->where('item_id', $kontrakan->id)
                               ->max('urutan') ?? 0;

            // Pastikan folder ada
            $folderPath = public_path('uploads/galeri/kontrakan');
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0755, true);
            }

            foreach ($request->file('fotos') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Simpan ke public/uploads/galeri/kontrakan/
                $file->move($folderPath, $filename);

                Galeri::create([
                    'type' => 'kontrakan',
                    'item_id' => $kontrakan->id,
                    'foto' => $filename,
                    'urutan' => ++$lastUrutan,
                    'is_primary' => false
                ]);

                $uploadedCount++;
            }
        }

        return back()->with('success', "$uploadedCount foto berhasil diupload");
    }

    /**
     * Upload foto untuk laundry
     */
    public function uploadLaundry(Request $request, Laundry $laundry)
    {
        $request->validate([
            'fotos' => 'required',
            'fotos.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $uploadedCount = 0;

        if ($request->hasFile('fotos')) {
            // Ambil urutan terakhir
            $lastUrutan = Galeri::where('type', 'laundry')
                               ->where('item_id', $laundry->id)
                               ->max('urutan') ?? 0;

            // Pastikan folder ada
            $folderPath = public_path('uploads/galeri/laundry');
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0755, true);
            }

            foreach ($request->file('fotos') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Simpan ke public/uploads/galeri/laundry/
                $file->move($folderPath, $filename);

                Galeri::create([
                    'type' => 'laundry',
                    'item_id' => $laundry->id,
                    'foto' => $filename,
                    'urutan' => ++$lastUrutan,
                    'is_primary' => false
                ]);

                $uploadedCount++;
            }
        }

        return back()->with('success', "$uploadedCount foto berhasil diupload");
    }

    /**
     * Set foto kontrakan sebagai primary/utama
     */
    public function setPrimaryKontrakan($id)
    {
        $galeri = Galeri::findOrFail($id);

        // Reset semua foto di kontrakan yang sama jadi bukan primary
        Galeri::where('type', 'kontrakan')
              ->where('item_id', $galeri->item_id)
              ->update(['is_primary' => false]);

        // Set foto ini sebagai primary
        $galeri->update(['is_primary' => true]);

        return back()->with('success', 'Foto utama berhasil diubah');
    }

    /**
     * Set foto laundry sebagai primary/utama
     */
    public function setPrimaryLaundry($id)
    {
        $galeri = Galeri::findOrFail($id);

        // Reset semua foto di laundry yang sama jadi bukan primary
        Galeri::where('type', 'laundry')
              ->where('item_id', $galeri->item_id)
              ->update(['is_primary' => false]);

        // Set foto ini sebagai primary
        $galeri->update(['is_primary' => true]);

        return back()->with('success', 'Foto utama berhasil diubah');
    }

    /**
     * Hapus foto kontrakan dari galeri
     */
    public function deleteKontrakan($id)
    {
        $galeri = Galeri::findOrFail($id);

        // Hapus file foto
        $filePath = public_path('uploads/galeri/kontrakan/' . $galeri->foto);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $galeri->delete();

        return back()->with('success', 'Foto berhasil dihapus');
    }

    /**
     * Hapus foto laundry dari galeri
     */
    public function deleteLaundry($id)
    {
        $galeri = Galeri::findOrFail($id);

        // Hapus file foto
        $filePath = public_path('uploads/galeri/laundry/' . $galeri->foto);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $galeri->delete();

        return back()->with('success', 'Foto berhasil dihapus');
    }
}
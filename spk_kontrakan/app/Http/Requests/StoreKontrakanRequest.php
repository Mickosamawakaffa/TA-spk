<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKontrakanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'no_whatsapp' => 'nullable|string|max:20|regex:/^[0-9]+$/',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'harga' => 'required|numeric|min:0',
            'jarak' => 'required|numeric|min:0',
            'fasilitas' => 'nullable|string|max:1000',
            'jumlah_kamar' => 'required|integer|min:1',
            'luas' => 'nullable|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kontrakan wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'no_whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka',
            'latitude.required' => 'Koordinat latitude harus diisi (klik pada peta)',
            'longitude.required' => 'Koordinat longitude harus diisi (klik pada peta)',
            'harga.required' => 'Harga wajib diisi',
            'harga.min' => 'Harga tidak boleh negatif',
            'jarak.required' => 'Jarak wajib diisi',
            'jumlah_kamar.required' => 'Jumlah kamar wajib diisi',
            'jumlah_kamar.min' => 'Jumlah kamar minimal 1',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format file harus jpeg, png, jpg, atau webp',
            'foto.max' => 'Ukuran foto maksimal 2MB',
        ];
    }
}

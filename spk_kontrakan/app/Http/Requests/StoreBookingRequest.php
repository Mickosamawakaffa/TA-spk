<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kontrakan_id' => 'required|exists:kontrakans,id',
            'tanggal_mulai' => 'required|date|after:today',
            'durasi_bulan' => 'required|integer|min:1|max:12',
            'catatan' => 'nullable|string|max:500',
            'payment_proof' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'kontrakan_id.required' => 'Kontrakan wajib dipilih',
            'kontrakan_id.exists' => 'Kontrakan tidak ditemukan',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'tanggal_mulai.after' => 'Tanggal mulai harus setelah hari ini',
            'durasi_bulan.required' => 'Durasi sewa wajib diisi',
            'durasi_bulan.min' => 'Durasi sewa minimal 1 bulan',
            'durasi_bulan.max' => 'Durasi sewa maksimal 12 bulan',
            'catatan.max' => 'Catatan maksimal 500 karakter',
            'payment_proof.required' => 'Bukti pembayaran wajib diunggah',
            'payment_proof.image' => 'File harus berupa gambar',
            'payment_proof.mimes' => 'Format file harus jpeg, jpg, atau png',
            'payment_proof.max' => 'Ukuran file maksimal 5MB',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Rating wajib diisi',
            'rating.integer' => 'Rating harus berupa angka bulat',
            'rating.min' => 'Rating minimal 1',
            'rating.max' => 'Rating maksimal 5',
            'komentar.max' => 'Komentar maksimal 500 karakter',
        ];
    }
}

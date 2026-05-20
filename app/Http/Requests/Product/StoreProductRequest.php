<?php

namespace App\Http\Requests\Product;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'game_id'       => ['required', 'integer', 'exists:games,id'],
            'name'          => ['required', 'string', 'max:255'],
            'slug'          => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'description'   => ['nullable', 'string'],
            'price'         => ['required', 'numeric', 'min:0'],
            'stock'         => ['nullable', 'integer', 'min:0'],
            'provider'      => ['nullable', 'string', 'max:100'],
            'provider_code' => ['nullable', 'string', 'max:100'],
            'type'          => ['required', Rule::in(['topup', 'item'])],
            'status'        => ['nullable', Rule::in(['active', 'inactive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'game_id.required' => 'Game wajib dipilih.',
            'game_id.exists'   => 'Game tidak ditemukan.',
            'name.required'    => 'Nama produk wajib diisi.',
            'slug.unique'      => 'Slug sudah digunakan.',
            'price.required'   => 'Harga wajib diisi.',
            'price.numeric'    => 'Harga harus berupa angka.',
            'price.min'        => 'Harga tidak boleh negatif.',
            'type.required'    => 'Tipe produk wajib dipilih.',
            'type.in'          => 'Tipe produk harus topup atau item.',
            'status.in'        => 'Status harus active atau inactive.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            ApiResponse::validationError($validator->errors())
        );
    }
}

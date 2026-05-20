<?php

namespace App\Http\Requests\Product;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            'game_id'       => ['sometimes', 'integer', 'exists:games,id'],
            'name'          => ['sometimes', 'string', 'max:255'],
            'slug'          => ['sometimes', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'description'   => ['nullable', 'string'],
            'price'         => ['sometimes', 'numeric', 'min:0'],
            'stock'         => ['nullable', 'integer', 'min:0'],
            'provider'      => ['nullable', 'string', 'max:100'],
            'provider_code' => ['nullable', 'string', 'max:100'],
            'type'          => ['sometimes', Rule::in(['topup', 'item'])],
            'status'        => ['sometimes', Rule::in(['active', 'inactive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'game_id.exists' => 'Game tidak ditemukan.',
            'slug.unique'    => 'Slug sudah digunakan.',
            'price.numeric'  => 'Harga harus berupa angka.',
            'price.min'      => 'Harga tidak boleh negatif.',
            'type.in'        => 'Tipe produk harus topup atau item.',
            'status.in'      => 'Status harus active atau inactive.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            ApiResponse::validationError($validator->errors())
        );
    }
}

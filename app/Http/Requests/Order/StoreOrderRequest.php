<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'target_id'  => ['required', 'string', 'max:100'],
            'quantity'   => ['sometimes', 'integer', 'min:1', 'max:100'],
            'notes'      => ['nullable', 'string', 'max:500'],
        ];

        // Jika user tidak login (guest)
        if (! $this->user('sanctum')) {
            $rules['guest_email'] = ['required', 'email', 'max:150'];
            $rules['guest_phone'] = ['nullable', 'string', 'max:20'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'product_id.required'  => 'Produk harus dipilih.',
            'product_id.exists'    => 'Produk tidak ditemukan.',
            'target_id.required'   => 'ID akun game wajib diisi.',
            'target_id.max'        => 'ID akun game maksimal 100 karakter.',
            'quantity.min'         => 'Jumlah minimal 1.',
            'quantity.max'         => 'Jumlah maksimal 100.',
            'guest_email.required' => 'Email wajib diisi untuk guest checkout.',
            'guest_email.email'    => 'Format email tidak valid.',
            'guest_email.max'      => 'Email maksimal 150 karakter.',
            'guest_phone.max'      => 'Nomor telepon maksimal 20 karakter.',
        ];
    }
}

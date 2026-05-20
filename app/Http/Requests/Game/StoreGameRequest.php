<?php

namespace App\Http\Requests\Game;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['nullable', 'string', 'max:255', 'unique:games,slug'],
            'thumbnail'   => ['nullable', 'string', 'url'],
            'banner'      => ['nullable', 'string', 'url'],
            'description' => ['nullable', 'string'],
            'type'        => ['required', Rule::in(['topup', 'item', 'both'])],
            'status'      => ['nullable', Rule::in(['active', 'inactive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => 'Nama game wajib diisi.',
            'slug.unique'     => 'Slug sudah digunakan.',
            'type.required'   => 'Tipe game wajib dipilih.',
            'type.in'         => 'Tipe game harus topup, item, atau both.',
            'status.in'       => 'Status harus active atau inactive.',
            'thumbnail.url'   => 'URL thumbnail tidak valid.',
            'banner.url'      => 'URL banner tidak valid.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            ApiResponse::validationError($validator->errors())
        );
    }
}

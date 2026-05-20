<?php

namespace App\Http\Requests\Game;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $gameId = $this->route('game');

        return [
            'name'        => ['sometimes', 'string', 'max:255'],
            'slug'        => ['sometimes', 'string', 'max:255', Rule::unique('games', 'slug')->ignore($gameId)],
            'thumbnail'   => ['nullable', 'string', 'url'],
            'banner'      => ['nullable', 'string', 'url'],
            'description' => ['nullable', 'string'],
            'type'        => ['sometimes', Rule::in(['topup', 'item', 'both'])],
            'status'      => ['sometimes', Rule::in(['active', 'inactive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique'   => 'Slug sudah digunakan.',
            'type.in'       => 'Tipe game harus topup, item, atau both.',
            'status.in'     => 'Status harus active atau inactive.',
            'thumbnail.url' => 'URL thumbnail tidak valid.',
            'banner.url'    => 'URL banner tidak valid.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            ApiResponse::validationError($validator->errors())
        );
    }
}

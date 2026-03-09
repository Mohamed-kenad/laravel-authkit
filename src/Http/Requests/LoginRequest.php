<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'       => ['required', 'string', 'email'],
            'password'    => ['required', 'string', 'min:8'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'platform'    => ['nullable', 'string', 'in:web,ios,android,desktop'],
            'abilities'   => ['nullable', 'array'],
            'abilities.*' => ['string', 'max:255'],
        ];
    }
}

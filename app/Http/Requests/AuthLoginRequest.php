<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\DTO\AuthLoginData;

class AuthLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function toDTO(): AuthLoginData
    {
        $data = $this->validated();

        return new AuthLoginData(
            email:    $data['email'],
            password: $data['password']
        );
    }
}

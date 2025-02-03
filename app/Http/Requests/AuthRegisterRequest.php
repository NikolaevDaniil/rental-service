<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\DTO\AuthRegisterData;

class AuthRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function toDTO(): AuthRegisterData
    {
        $data = $this->validated();

        return new AuthRegisterData(
            name:     $data['name'],
            email:    $data['email'],
            password: $data['password']
        );
    }
}

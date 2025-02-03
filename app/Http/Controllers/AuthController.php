<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\AuthRegisterRequest;
use App\Http\Requests\AuthLoginRequest;

class AuthController extends Controller
{
    public function register(AuthRegisterRequest $request): JsonResponse
    {
        $dto = $request->toDTO();

        $user = User::query()->create([
            'name'     => $dto->name,
            'email'    => $dto->email,
            'password' => Hash::make($dto->password),
            'balance'  => 1000
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return new JsonResponse(['token' => $token], 201);
    }

    public function login(AuthLoginRequest $request): JsonResponse
    {
        $dto = $request->toDTO();

        $user = User::query()->where('email', $dto->email)->first();
        if (!$user || !Hash::check($dto->password, $user->password)) {
            return new JsonResponse(['error' => 'Неверные учетные данные'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return new JsonResponse(['token' => $token], 200);
    }
}

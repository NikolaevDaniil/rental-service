<?php

namespace App\DTO;

readonly class AuthLoginData
{
    public function __construct(
        public string $email,
        public string $password
    ) {}
}

<?php

namespace App\DTO;

readonly class AuthRegisterData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {}
}

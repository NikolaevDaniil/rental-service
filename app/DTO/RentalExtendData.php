<?php

namespace App\DTO;

readonly class RentalExtendData
{
    public function __construct(
        public int $rental_id,
        public int $duration // продление в часах
    ) {}
}

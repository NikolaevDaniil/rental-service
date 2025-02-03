<?php

namespace App\DTO;

readonly class RentalData
{
    public function __construct(
        public int $product_id,
        public int $duration // в часах (4,8,12,24)
    ) {}
}

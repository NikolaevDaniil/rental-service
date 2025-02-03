<?php

namespace App\DTO;

readonly class PurchaseData
{
    public function __construct(
        public int $product_id,
    ) {}
}

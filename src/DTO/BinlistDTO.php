<?php

namespace App\DTO;

class BinlistDTO
{
    public function __construct(
        public readonly string $bin,
        public readonly string $type,
        public readonly string  $brand,
        public readonly string $countryCodeAlpha2,
        public readonly string $currency,
    ) {}
}
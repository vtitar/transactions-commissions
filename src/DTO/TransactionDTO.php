<?php

namespace App\DTO;

class TransactionDTO
{
    public function __construct(
        public readonly string $bin,
        public readonly float  $amount,
        public readonly string $currency,
    ) {}
}
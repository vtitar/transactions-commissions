<?php

namespace App\Service\ExchangeRateReader;

interface ExchangeRateReaderInterface
{
    public function getExchangeRate(string $currencyCode): float;
}
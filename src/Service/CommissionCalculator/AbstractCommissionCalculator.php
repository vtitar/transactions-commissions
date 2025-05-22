<?php

namespace App\Service\CommissionCalculator;

use App\DTO\TransactionDTO;
use App\Service\ExchangeRateReader\ExchangeRateReaderInterface;

abstract class AbstractCommissionCalculator implements CommissionCalculatorInterface
{
    const FEE = 0.0;

    private TransactionDTO $transactionDTO;

    public function __construct(
        private readonly ExchangeRateReaderInterface $exchangeRateReader,
    ) {
    }

    public function calculate(TransactionDTO $transactionDTO): float
    {
        $this->transactionDTO = $transactionDTO;

        $exchangeRate = $this->getExchangeRate();
        $fee = $this->getFee();

        $commission = $transactionDTO->amount / $exchangeRate * $fee;

        return $this->ceilToCents($commission);
    }

    public function getExchangeRate(): float
    {
        return $this->exchangeRateReader->getExchangeRate($this->transactionDTO->currency);
    }

    protected function ceilToCents(float $value): float
    {
        return ceil($value * 100) / 100;
    }

    public function getFee(): float
    {
        return static::FEE;
    }

}
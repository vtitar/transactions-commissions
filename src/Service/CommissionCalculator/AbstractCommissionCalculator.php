<?php

namespace App\Service\CommissionCalculator;

use App\DTO\TransactionDTO;

abstract class AbstractCommissionCalculator implements CommissionCalculatorInterface
{
    const FEE = 0.0;

    public function __construct(

    ) {
    }

    public function calculate(TransactionDTO $transactionDTO): float
    {
        $exchangeRate = $this->getExchangeRate();
        if ($exchangeRate == 0) {
            throw new \Exception('Exchange rate cannot be zero');
        }

        $fee = $this->getFee();

        $commission = $transactionDTO->amount / $exchangeRate * $fee;

        return $this->ceilToCents($commission);
    }

    public function getExchangeRate(): float
    {
        //TODO: add real exchage rate
        return 1.1;
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
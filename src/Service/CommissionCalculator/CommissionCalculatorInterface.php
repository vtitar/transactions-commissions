<?php

namespace App\Service\CommissionCalculator;

use App\DTO\TransactionDTO;

interface CommissionCalculatorInterface
{
    public function calculate(TransactionDTO $transactionDTO): float;
    public function getExchangeRate(): float;
    public function getFee(): float;

}
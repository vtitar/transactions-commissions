<?php

namespace App\Service\CommissionCalculator\Factory;

use App\Service\CommissionCalculator\CommissionCalculatorInterface;

interface CommissionCalculatorFactoryInterface
{
    public function makeCalculator(string $countryCode): CommissionCalculatorInterface;

}
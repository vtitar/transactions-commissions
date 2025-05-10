<?php

namespace App\Service\CommissionCalculator\Factory;

use App\Service\CommissionCalculator\CommissionCalculatorEu;
use App\Service\CommissionCalculator\CommissionCalculatorInterface;
use App\Service\CommissionCalculator\CommissionCalculatorOther;
use App\Service\RegionZone\RegionZoneDetectorInterface;
use App\Enum\RegionZoneCode;

class CommissionCalculatorFactory implements CommissionCalculatorFactoryInterface
{
    public function __construct(
        private readonly RegionZoneDetectorInterface $zoneDetector
    ) {
    }

    public function makeCalculator(string $countryCode): CommissionCalculatorInterface
    {
        $countryZone = $this->zoneDetector->getZone($countryCode);

        switch ($countryZone) {
            case RegionZoneCode::ZONE_EU:
                return new CommissionCalculatorEU();
            default:
                return new CommissionCalculatorOther();
        }
    }

}
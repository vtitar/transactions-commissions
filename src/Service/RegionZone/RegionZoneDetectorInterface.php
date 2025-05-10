<?php

namespace App\Service\RegionZone;

use App\Enum\RegionZoneCode;

interface RegionZoneDetectorInterface
{
    public function isEuCountry(string $countryCode): bool;

    public function getZone(string $countryCode): RegionZoneCode;

}
<?php

namespace App\Service\RegionZone;

interface RegionZoneDetectorInterface
{
    public function isEuCountry(string $countryCode): bool;

    public function getZone(string $countryCode): string;

}
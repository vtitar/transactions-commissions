<?php

namespace App\Service\RegionZone;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use App\Enum\RegionZoneCode;

class RegionZoneDetector implements RegionZoneDetectorInterface
{

    public function __construct(
        private readonly ContainerBagInterface $containerBag
    ) {
    }

    public function isEuCountry(string $countryCode): bool
    {
        $euCountryCodes = $this->getEUCountriesCodes();
        return in_array(strtoupper($countryCode), $euCountryCodes);
    }

    protected function getEUCountriesCodes(): array
    {
        return $this->containerBag->get('region-zone.eu.countries-list');
    }

    public function getZone(string $countryCode): RegionZoneCode
    {
        //TODO: would be nice to move this to fabrica and polymorfizm
        if ($this->isEuCountry($countryCode)) {
            return RegionZoneCode::ZONE_EU;
        }

        return RegionZoneCode::ZONE_OTHER;
    }
}
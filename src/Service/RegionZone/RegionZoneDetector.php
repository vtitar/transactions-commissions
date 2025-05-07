<?php

namespace App\Service\RegionZone;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class RegionZoneDetector implements RegionZoneDetectorInterface
{
    private const ZONE_EU = 'EU';
    private const ZONE_OTHER = 'Other';

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

    public function getZone(string $countryCode): string
    {
        //TODO: would be nice to move this to fabrica and polymorfizm
        if ($this->isEuCountry($countryCode)) {
            return self::ZONE_EU;
        }

        return self::ZONE_OTHER;
    }
}
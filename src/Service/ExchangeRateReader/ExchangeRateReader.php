<?php

namespace App\Service\ExchangeRateReader;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class ExchangeRateReader implements ExchangeRateReaderInterface
{
    const CACHE_KEY_EUR = 'exchange_rate_eur';
    const BASE_CURRENCY = 'EUR';

    private string $currencyCode = '';

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly ContainerBagInterface $containerBag,
        private readonly LoggerInterface $logger
    ) {
    }

    public function getExchangeRate(string $currencyCode): float
    {
        $this->currencyCode = $currencyCode;

        $exchangeRate = $this->getExchangeRateToEur();

        return $exchangeRate;
    }

    protected function getExchangeRateToEur(): float
    {
        $ratesResponse = $this->getRatesResponseFromApi();

        if (!isset($ratesResponse['rates'][$this->currencyCode])) {
            throw new \Exception('Exchange rate if not found for currency code: ' . $this->currencyCode);
        }

        $exchangeRate = (float) $ratesResponse['rates'][$this->currencyCode];

        if (!$exchangeRate) {
            throw new \Exception('Exchange rate is not valid.');
        }

        return $exchangeRate;
    }

    protected function getRatesResponseFromApi(): array
    {
        $cache = new FilesystemAdapter();

        $ratesJson = $cache->get(self::CACHE_KEY_EUR, function (ItemInterface $item): string {
            $item->expiresAfter($this->containerBag->get('exchangerates.api.cache-lifetime-seconds'));

            $rates = $this->getRatesFromApi();

            return $rates;
        });

        $rates = json_decode($ratesJson, true);

        return $rates;
    }

    private function getRatesFromApi(): string
    {
        $apiEndpoint = $this->getApiEndpoint();
        try {
            $response = $this->client->request('GET', $apiEndpoint);
            $responseContent = $response->getContent();

            $this->validateResponse($response->toArray());

            return $responseContent;
        } catch (TransportExceptionInterface $exception) {
            $this->logger->error('Cant get Exchange Rates from API: ' . $exception->getMessage());

            throw new \RuntimeException('Error fetching ExchageRates: ' . $exception->getMessage());
        }
    }

    private function getApiEndpoint(): string
    {
        $endpoint = $this->containerBag->get('exchangerates.api.endpoint');
        $accessKey = $this->containerBag->get('exchangerates.api.access-key');

        $apiEndpoint = $endpoint . '?access_key=' . $accessKey . '&format=1';
        return $apiEndpoint;
    }

    private function validateResponse(array $ratesResponse): void
    {
        if ($ratesResponse['base'] != self::BASE_CURRENCY) {
            throw new \Exception('Rate response is for wrong currency code: ' . $ratesResponse['base']);
        }
    }
}
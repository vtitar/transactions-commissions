<?php

namespace App\Service\BinlistReader;

use App\DTO\BinlistDTO;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class BinlistReader implements BinlistReaderInterface
{
    const BINLIST_CACHE_KEY_PREFIX = "binlist_data_";

    private string $bin;

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly ContainerBagInterface $containerBag,
        private readonly LoggerInterface $logger
    ) {
    }

    public function getData(string $bin): BinlistDTO
    {
        $this->bin = $bin;

        $responseArray = $this->getBinlistResponseArray();
        $binlistDTO = $this->transformToDto($responseArray);

        return $binlistDTO;
    }

    protected function getBinlistResponseArray(): array
    {
        $cache = new FilesystemAdapter();

        $binCacheKey = $this->getBinCacheKey();

        $responseContent = $cache->get($binCacheKey, function (ItemInterface $item): string {
            $item->expiresAfter($this->containerBag->get('binlist.api.cache-lifetime-seconds'));

            $dataFromBinlist = $this->getDataFromBilist();

            return $dataFromBinlist;
        });

        $responseArray = json_decode($responseContent, true);
        $this->validateResponseData($responseArray);

        return $responseArray;
    }

    private function getDataFromBilist(): string
    {
        try {
            $maxRetries = $this->containerBag->get('binlist.api.max-retries');
            $retryDelaySeconds = $this->containerBag->get('binlist.api.retry-delay-seconds');

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                $binApiEndpoint = $this->getBinApiEndpoint();

                $response = $this->client->request('GET', $binApiEndpoint);
                $statusCode = $response->getStatusCode();

                if ($statusCode === Response::HTTP_TOO_MANY_REQUESTS) {
                    if ($attempt < $maxRetries) {
                        sleep($retryDelaySeconds);
                        continue;
                    } else {
                        throw new \RuntimeException("Max retries reached due to 429.");
                    }
                }

                $responseContent = $response->getContent();

                return $responseContent;
            }
        } catch (TransportExceptionInterface $exception) {
            $this->logger->error('Cant get binlist data for bin: ' . $this->bin);

            throw new \RuntimeException('Error fetching data: ' . $exception->getMessage());
        }

        throw new \RuntimeException('Error fetching data for bin: ' . $this->bin);
    }

    protected function validateResponseData(array $responseArray): void
    {
        if (!isset($responseArray['country']['alpha2'])) {
            throw new \Exception('Binlist response does not contain country code');
        }
    }

    protected function getBinCacheKey(): string
    {
        return self::BINLIST_CACHE_KEY_PREFIX . $this->bin;
    }

    private function getBinApiEndpoint(): string
    {
        $binApiEndpoint = $this->containerBag->get('binlist.api.endpoint') . $this->bin;
        return $binApiEndpoint;
    }

    private function transformToDto(array $data): BinlistDTO
    {
        // TODO: might be nice to add DTO validator

        $binlistDTO = new BinlistDTO(
            $this->bin,
            (string) $data['type'] ?? '',
            (string) $data['brand'] ?? '',
            (string) $data['country']['alpha2'] ?? '',
            (string) $data['country']['currency'] ?? '',
        );

        return $binlistDTO;
    }
}
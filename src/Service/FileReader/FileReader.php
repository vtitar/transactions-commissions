<?php

namespace App\Service\FileReader;

use App\Service\FileDataTransformer\FileDataTransformerInterface;
use App\Service\FileReader\FileReaderInterface;
use Psr\Log\LoggerInterface;

class FileReader implements FileReaderInterface
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public function readFile(string $filePath, FileDataTransformerInterface $transformer): \Generator
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \RuntimeException("File not found or not readable: $filePath");
        }

        $handle = fopen($filePath, 'r');

        if (!$handle) {
            throw new \RuntimeException("Unable to open file: $filePath");
        }

        while (($line = fgets($handle)) !== false) {
            try {
                $dto = $transformer->transform(trim($line));
                yield $dto;
            } catch (\Throwable $exception) {

                $this->logger->error('Line reading is failed', [
                    'line' => $line,
                    $exception
                ]);

            }
        }

        fclose($handle);
    }
}
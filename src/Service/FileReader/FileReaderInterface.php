<?php

namespace App\Service\FileReader;

use App\Service\FileDataTransformer\FileDataTransformerInterface;

interface FileReaderInterface
{
    public function readFile(string $filePath, FileDataTransformerInterface $transformer): \Generator;
}
<?php

namespace App\Service\FileDataTransformer;

interface FileDataTransformerInterface
{
    public function transform(string $line): ?object;
}
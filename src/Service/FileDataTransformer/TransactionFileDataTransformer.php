<?php

namespace App\Service\FileDataTransformer;

use App\DTO\TransactionDTO;
use App\Service\FileDataTransformer\FileDataTransformerInterface;

class TransactionFileDataTransformer implements FileDataTransformerInterface
{
    public function transform(string $line): ?TransactionDTO
    {
        $data = json_decode($line, true);

        if (!is_array($data)) {
            throw new \Exception($line. ' is not a valid JSON object');
        }

        if (!isset($data['bin'], $data['amount'], $data['currency'])) {
            throw new \Exception($line . ': missing or invalid fields');
        }

        //TODO: might be nice to add DTO validator

        return new TransactionDTO(
            (string) $data['bin'],
            (float) $data['amount'],
            (string) $data['currency']
        );
    }
}
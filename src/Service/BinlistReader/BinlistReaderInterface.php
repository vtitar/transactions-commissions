<?php

namespace App\Service\BinlistReader;

use App\DTO\BinlistDTO;

interface BinlistReaderInterface
{
    public function getData(string $bin): BinlistDTO;
}
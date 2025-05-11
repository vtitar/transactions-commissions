<?php

namespace App\Tests\TransactionsCommissions\MockServices;

use App\DTO\BinlistDTO;
use App\Service\BinlistReader\BinlistReaderInterface;
use App\Service\ExchangeRateReader\ExchangeRateReaderInterface;
use App\Tests\TransactionsCommissions\AbstractTester;

class SuccessTest extends AbstractTester
{

    public function testSuccess()
    {
        parent::execute();

        $this->assertStringContainsString($this->normilize($this->getExpectedOutput()), $this->normilize($this->output));
    }

    protected function mock(): void
    {
        $this->mockBinlistReader();
        $this->mockExchangeRateReader();
    }

    protected function mockBinlistReader(): void
    {
        $mock = $this->createMock(BinlistReaderInterface::class);

        $binlistDTO1 = new BinlistDTO(
            '45717363',
            'debit',
            'Visa Classic/Dankort',
            'DK',
            'DKK',
        );

        $binlistDTO2 = new BinlistDTO(
            '5167963',
            'debit',
            'Debit Mastercard',
            'LT',
            'EUR',
        );

        $binlistDTO3 = new BinlistDTO(
            '45417363',
            'credit',
            'Visa Classic',
            'JP',
            'JPY',
        );

        $binlistDTO4 = new BinlistDTO(
            '4745033',
            'debit',
            'Visa Classic',
            'LT',
            'EUR',
        );

        $mock->method('getData')
            ->will($this->onConsecutiveCalls($binlistDTO1, $binlistDTO2, $binlistDTO3, $binlistDTO4));

        $this->container->set(BinlistReaderInterface::class, $mock);
    }

    protected function mockExchangeRateReader(): void
    {
        $mock = $this->createMock(ExchangeRateReaderInterface::class);

        $mock->method('getExchangeRate')
            ->will($this->onConsecutiveCalls(1, 1.125361, 163.66573, 0.845881));

        $this->container->set(ExchangeRateReaderInterface::class, $mock);
    }

    protected function getExpectedOutput(): string
    {
        return
            "1
0.45
1.23
23.65";
    }

    protected function getFilePath(): string
    {
        return __DIR__ . '/input.txt';
    }
}
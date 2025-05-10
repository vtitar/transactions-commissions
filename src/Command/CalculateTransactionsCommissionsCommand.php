<?php

namespace App\Command;

use App\DTO\TransactionDTO;
use App\Service\BinlistReader\BinlistReaderInterface;
use App\Service\CommissionCalculator\Factory\CommissionCalculatorFactoryInterface;
use App\Service\FileDataTransformer\FileDataTransformerInterface;
use App\Service\FileReader\FileReaderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use \Throwable;


#[AsCommand(
    name: 'app:transactions:calculate-commissions',
    description: 'Calculate commissions for transactions.',
    hidden: false
)]
class CalculateTransactionsCommissionsCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private SymfonyStyle $io;

    private string $filePath;

    public function __construct(
        private readonly FileReaderInterface                    $fileReader,
        private readonly FileDataTransformerInterface           $fileDataTransformer,
        private readonly BinlistReaderInterface                 $binlistReader,
        private readonly CommissionCalculatorFactoryInterface   $commissionCalculatorFactory,
        private readonly LoggerInterface                        $logger
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'filepath',
            InputArgument::REQUIRED,
            'Path to the input file'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->io = $io;

        $start = time();
        $datetime = new \DateTime();
        $formatedDateTime = $datetime->format('Y-m-d H:i:s');

        $this->io->info("Started: $formatedDateTime");

        $this->filePath = $input->getArgument('filepath');

        $this->process();

        $end = time();
        $duration = $end - $start;

        $io->success("Finished in $duration seconds");

        return Command::SUCCESS;
    }

    private function process(): void
    {
        try {
            $this->calculateCommissions();
        } catch (Throwable $exception) {
            $this->handleException('Command is failed', $exception);
        }
    }

    private function calculateCommissions(): void
    {
        foreach ($this->fileReader->readFile($this->filePath, $this->fileDataTransformer) as $dto) {
            try {
                $this->calculateCommissionForTransaction($dto);
            } catch (Throwable $exception) {
                $this->handleException('Cant calculate commission for transaction', $exception);
            }
        }
    }

    private function calculateCommissionForTransaction(TransactionDto $transactionDTO): void
    {
        $binlistDTO = $this->binlistReader->getData($transactionDTO->bin);

        $commissionCalculator = $this->commissionCalculatorFactory->makeCalculator($binlistDTO->countryCodeAlpha2);

        $commissionValue = $commissionCalculator->calculate($transactionDTO);

        $this->io->write($commissionValue);
        $this->io->newLine();
    }

    private function handleException(string $message, Throwable $exception): void
    {
        //TODO: handle this correctly

        $this->logger->error($message, [
            $exception
        ]);
    }
}
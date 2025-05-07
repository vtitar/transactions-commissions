<?php

namespace App\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


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

    public function __construct(
        private readonly LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->io = $io;

        $start = time();
        $datetime = new \DateTime();
        $formatedDateTime = $datetime->format('Y-m-d H:i:s');

        $this->io->info("Started: $formatedDateTime");

        try {
            $this->process();
        } catch (\Throwable $exception) {
            //TODO: handle this correctly
            $this->io->error($exception->getMessage());

            $this->logger->critical('Command is failed', [
                'exception' => $exception->getMessage(),
                $exception
            ]);
        }

        $end = time();
        $duration = $end - $start;

        $io->success("Finished in $duration seconds");

        return Command::SUCCESS;
    }

    private function process(): void
    {
        //read file

        //get binlist

        //get rate

        //calculate commission
    }
}
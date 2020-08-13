<?php

namespace App\Command;

use App\Component\Steps\AllSteps;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ImportStartCommand
 * @package App\Command
 */
class ImportStartCommand extends Command
{
    protected static $defaultName = 'import:start';

    /** @var LoggerInterface LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger, string $name = null)
    {
        parent::__construct($name);
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setDescription('Import command, do  not use directly without the controller')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->logger->info('Command import:start has been launched');
        AllSteps::execute($this->logger);
        $io->success("You have successfully imported all the data in the new database.");

        return 0;
    }
}

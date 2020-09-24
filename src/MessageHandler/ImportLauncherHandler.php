<?php


namespace App\MessageHandler;


use App\Component\Steps\AllSteps;
use App\Message\ImportLauncher;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ImportLauncherHandler implements MessageHandlerInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(ImportLauncher $importLauncher)
    {
        AllSteps::execute($this->logger);
    }
}
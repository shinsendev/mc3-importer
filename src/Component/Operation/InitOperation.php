<?php


namespace App\Component\Operation;


use App\Component\ImportEntity\ImportEntityManager;
use App\Component\MySQL\Initialization\MySQLInitialization;
use Psr\Log\LoggerInterface;

class InitOperation
{
    public static function init(LoggerInterface $logger):void
    {
        MySQLInitialization::init();
        $logger->info('All MySQL tables have been deleted');
        ImportEntityManager::updateImportEntity(ImportEntityManager::STARTED_STATUS, true);
        $logger->info('Import entity has a started status');
    }
}
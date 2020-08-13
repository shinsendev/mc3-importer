<?php

declare(strict_types=1);


namespace App\Component\Steps;


use App\Component\ImportEntity\ImportEntityManager;
use App\Component\MySQL\Clean\MySQLClean;
use App\Component\PostgreSQL\Clean\PgSQLClean;
use Psr\Log\LoggerInterface;

class PostProcessStep implements StepInterface
{
    static function execute(LoggerInterface $logger)
    {
        MySQLClean::finish();
        PgSQLClean::finish();
        ImportEntityManager::updateImportEntity(ImportEntityManager::SUCCESS_STATUS);
    }

}
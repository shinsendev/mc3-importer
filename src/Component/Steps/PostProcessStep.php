<?php

declare(strict_types=1);


namespace App\Component\Steps;


use App\Component\ImportEntity\ImportEntityManager;
use App\Component\Manual\ManuelUpdate;
use App\Component\MySQL\Clean\MySQLClean;
use App\Component\PostgreSQL\Clean\PgSQLClean;
use Psr\Log\LoggerInterface;

class PostProcessStep implements StepInterface
{
    static function execute(LoggerInterface $logger)
    {
        // add manual updates
        ManuelUpdate::execute();

        // clean
        MySQLClean::finish();
        PgSQLClean::finish();

        // update import entity
        ImportEntityManager::updateImportEntity(ImportEntityManager::SUCCESS_STATUS, 0);
    }

}
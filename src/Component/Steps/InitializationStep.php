<?php


namespace App\Component\Steps;


use App\Component\MySQL\Import\MySQLImport;
use App\Component\Operation\CleanOperation;
use App\Component\Operation\InitOperation;
use Psr\Log\LoggerInterface;

/**
 * Class InitializationStep
 * @package App\Component\Steps
 */
class InitializationStep implements StepInterface
{
    const SQL_FILE_NAME = 'mc2.sql';

    public static function execute(LoggerInterface $logger)
    {
        // delete MC2 MySQL data if exists and create a new one
        InitOperation::init($logger);
        $logger->info('Init operations just finished');

        // import all tables and data into MySQL MC2 database
        $logger->info('sql file path is :'.$_ENV['DATA_FILE_PATH'].self::SQL_FILE_NAME);
        MySQLImport::import($_ENV['DATA_FILE_PATH'].self::SQL_FILE_NAME);
        $logger->info('MySQL initalization import finished.');

        // clean data from PSQL DataBase and remove useless MySQL tables
        CleanOperation::clean();
        $logger->info('Clean has been done in Psql and MySQL tables.');
    }
}
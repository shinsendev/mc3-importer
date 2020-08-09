<?php


namespace App\Component\Steps;


use App\Component\MySQL\Import\MySQLImport;
use App\Component\Operation\CleanOperation;
use App\Component\Operation\InitOperation;

/**
 * Class InitializationStep
 * @package App\Component\Steps
 */
class InitializationStep
{
    const SQL_FILE_DIR = '../data/mc2.sql';

    public static function execute()
    {
        // delete MC2 MySQL data if exists and create a new one
        InitOperation::init();

        // import all tables and data into MySQL MC2 database
        MySQLImport::import(self::SQL_FILE_DIR);

        // clean data from PSQL DataBase and remove useless MySQL tables
        CleanOperation::clean();
    }
}
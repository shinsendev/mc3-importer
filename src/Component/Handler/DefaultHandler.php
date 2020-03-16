<?php

declare(strict_types=1);


namespace App\Component\Handler;

use App\Component\MySQL\Import\ImportDatabaseFromSQLFile;
use App\Component\MySQL\Initialization\MySQLInitialization;

class DefaultHandler
{
    public static function handle()
    {
        // we initialize = remove old mysql mc2 db and create new one
        $response = MySQLInitialization::init();

        // we import = add tables and data to new mc2 db with the file content in data
        ImportDatabaseFromSQLFile::import($response);

        // we delete useless data

        // we add some uuid columns

        // we export the data in CSV files

        return $response;
    }
}
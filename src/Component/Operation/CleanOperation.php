<?php


namespace App\Component\Operation;


use App\Component\MySQL\Clean\MySQLClean;
use App\Component\PostgreSQL\Clean\PgSQLClean;

class CleanOperation
{
    public static function clean():void
    {
        PgSQLClean::clean();
        MySQLClean::clean();
    }
}
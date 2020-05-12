<?php

declare(strict_types=1);


namespace App\Component\PostgreSQL\Connection;


class PostgreSQLConnection
{
    static function connection()
    {
        try {
            $connection = new \PDO("pgsql:host=".$_SERVER['PGSQL_SERVER'].";port=".$_SERVER['PGSQL_PORT'].";dbname=".$_SERVER['PGSQL_DB'], $_SERVER['PGSQL_USER'], $_SERVER['PGSQL_PWD'], []);

            // set the PDO error mode to exception
            $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        catch(\PDOException $e) {
            throw new \Error($e->getMessage());
        }

        return $connection;
    }
}
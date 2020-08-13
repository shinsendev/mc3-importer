<?php

declare(strict_types=1);


namespace App\Component\MySQL\Connection;


class MySQLConnection
{
    static function connection(): \PDO
    {
        try {
            $host = $_SERVER['MYSQL_SERVER'];
            $db = $_SERVER['MYSQL_DB'];
            $connection = new \PDO("mysql:host=$host;dbname=$db", $_SERVER['MYSQL_USER'], $_SERVER['MYSQL_PWD'], []);

            // set the PDO error mode to exception
            $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        }
        catch(\PDOException $e) {
            throw new \Error($e->getMessage());
        }

        return $connection;
    }
}
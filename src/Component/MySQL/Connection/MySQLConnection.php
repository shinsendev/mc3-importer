<?php

declare(strict_types=1);


namespace App\Component\MySQL\Connection;


class MySQLConnection
{
    static function connection(string $servername, string $username, string $password, string $dbName = null)
    {
        try {
            $connection = new \PDO("mysql:host=$servername;dbname=$dbName", $username, $password, []);
            // set the PDO error mode to exception
            $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        catch(\PDOException $e) {
            throw new \Error($e->getMessage());
        }

        return $connection;
    }
}
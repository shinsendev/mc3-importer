<?php

declare(strict_types=1);


namespace App\Component\MySQL\Initialization;


use App\Component\MySQL\Connection\MySQLConnection;

class MySQLInitialization
{
    static function init():void
    {
        $connection = MySQLConnection::connection(); // if not exists, add a command to create
        $sql = "CREATE DATABASE IF NOT EXISTS mc2";
        $connection->query($sql);

        // remove all table for mc2 database
        self::dropTables($connection);
    }

    public static function dropTables(\PDO $connection)
    {
        //todo: add logs

        // select all mysql tables for deleting them
        $sql = "SELECT CONCAT('DROP TABLE ', table_name, ';') AS 'command' FROM information_schema.tables WHERE table_schema = 'mc2'; ";
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $operations = $stmt->fetchAll();

        $operationsLength = count($operations);
        $i = 0;

        foreach ($operations as $operation) {
            // if it's the first request we desactivate the constraints
            if ($i == 0) {
                $connection->query('SET FOREIGN_KEY_CHECKS = 0; '.$operation['command']);
                ++$i;
            } else if ($i == $operationsLength -1) { // we reactivate the constraints at the end
                $connection->query($operation['command'].'SET FOREIGN_KEY_CHECKS = 1;');
            } else {
                $connection->query($operation['command']);
                ++$i;
            }
        }
    }

}
<?php

declare(strict_types=1);


namespace App\Component\MySQL\Initialization;


use App\Component\MySQL\Connection\MySQLConnection;

class MySQLInitialization
{
    static function init():void
    {
        $connection = MySQLConnection::connection();

        $sql = "DROP DATABASE IF EXISTS mc2";
        $connection->query($sql);

        $sql = "CREATE DATABASE IF NOT EXISTS mc2";
        $connection->query($sql);
    }
}
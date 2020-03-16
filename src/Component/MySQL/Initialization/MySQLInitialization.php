<?php

declare(strict_types=1);


namespace App\Component\MySQL\Initialization;


use App\Component\MySQL\Connection\MySQLConnection;

class MySQLInitialization
{
    public static function init()
    {
        $connection = MySQLConnection::connection("127.0.0.1:8889", "root", "root");

        $sql = "DROP DATABASE IF EXISTS mc2";
        $connection->query($sql);

        $sql = "CREATE DATABASE IF NOT EXISTS mc2";
        $connection->query($sql);

        $connection = MySQLConnection::connection("127.0.0.1:8889", "root", "root", "mc2");


        return $connection;
    }
}
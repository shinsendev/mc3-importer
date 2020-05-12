<?php

declare(strict_types=1);


namespace App\Component\Migration;


use App\Component\MySQL\Connection\MySQLConnection;
use App\Component\PostgreSQL\Connection\PostgreSQLConnection;

class ImportFilms
{
    /**
     * @param int $offset
     * @param int $max
     * @return bool
     */
    static public function importBulk($offset = 0, $max = 100)
    {
        // connect to MySQL and get films list
        $mysql = MySQLConnection::connection();

        $sql = 'SELECT * FROM film LIMIT 100;';
        foreach  ($mysql->query($sql) as $row) {
            $films[] = $row;
        }

        // connect to PostgreSQL and insert the usefull data of the list
        $psql = PostgreSQLConnection::connection();
        $sql = "INSERT INTO film (title, uuid) VALUES ('hello', '10b7a5d6-70cd-4d0a-ba12-56d8744e46db');";
        $psql->query($sql);

        return true;
    }
}
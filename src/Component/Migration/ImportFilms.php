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
    static public function importBulk($offset = 0, $limit = 100)
    {
        // connect to MySQL and get films list
        $mysql = MySQLConnection::connection();

        // extract film list in an array (we need to pass int $limit and $offset in a special way for mysql and not as parameters: https://stackoverflow.com/questions/10014147/limit-keyword-on-mysql-with-prepared-statement)
        $sql = sprintf('SELECT * FROM film LIMIT %d, %d', $offset, $limit);
        $stmt = $mysql->prepare($sql);
        $stmt->execute();
        $films = $stmt->fetchAll();

        // connect to PostgreSQL and insert the usefull data of the list
        $psql = PostgreSQLConnection::connection();

        // the we insert the films one by one
        foreach ($films as $film) {
            $sql = "INSERT INTO film (title, uuid, production_year, released_year, imdb, length, remake) VALUES (:title, '10b7a5d6-70cd-4d0a-ba12-56d8744e46db', :productionyear, :released, :imdb, :length, :remake)";
            $rsl = $psql->prepare($sql);
            $rsl->execute([
                // set correct values
                'title' => ($film['title']) ? $film['title'] : null,
                'productionyear' => ($film['productionyear']) ? $film['productionyear'] : null,
                'released' => ($film['released']) ? $film['released'] : null,
                'imdb' => ($film['id_imdb']) ? $film['id_imdb'] : null,
                'length' => ($film['length']) ? $film['length'] : null,
                'remake' => ($film['remake']) ? $film['remake'] : null,
            ]);
        }

        dd($films[0]);

        return true;
    }

    static public function insertFilm() {
        //todo
    }
}
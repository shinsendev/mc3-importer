<?php

declare(strict_types=1);

namespace App\Component\Migration;

use App\Component\MySQL\Connection\MySQLConnection;
use App\Component\PostgreSQL\Connection\PostgreSQLConnection;
use Ramsey\Uuid\Uuid;

class ImportFilms
{
    /**
     * @param $connection
     * @param $film
     */
    static public function insert($connection, $film):void
    {
        $uuid = Uuid::uuid4()->toString();
        $date = new \DateTime();
        $date = $date->format('Y-m-d H:i:s');

        $sql = "INSERT INTO film (title, uuid, production_year, released_year, imdb, length, remake, pca, created_at, updated_at) VALUES (:title, :uuid, :productionYear, :released, :imdb, :length, :remake, :pca, :createdAt, :updatedAt)";
        $rsl = $connection->prepare($sql);
        $rsl->execute([
            // set correct values
            'title' => ($film['title']) ? $film['title'] : null,
            'productionYear' => ($film['productionyear']) ? $film['productionyear'] : null,
            'released' => ($film['released']) ? $film['released'] : null,
            'imdb' => ($film['id_imdb']) ? $film['id_imdb'] : null,
            'length' => ($film['length']) ? $film['length'] : null,
            'remake' => ($film['remake']) ? $film['remake'] : null,
            'pca' => ($film['pca_texte']) ? $film['pca_texte'] : null,
            'createdAt' => $date,
            'updatedAt' => $date,
            'uuid' => $uuid
        ]);
    }
}
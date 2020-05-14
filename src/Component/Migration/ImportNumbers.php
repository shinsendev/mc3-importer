<?php

declare(strict_types=1);

namespace App\Component\Migration;

use App\Component\Migration\Helper\MigrationHelper;

/**
 * Class ImportNumbers
 * @package App\Component\Migration
 */
class ImportNumbers implements ImporterInterface
{
    static public function insert(\PDO $pgsql, array $itemsList, \PDO $mysql)
    {
        $basics = MigrationHelper::createBaseParams();

//        $sql = "INSERT INTO number (title, film_id, uuid, production_year, released_year, imdb, length, remake, pca, created_at, updated_at) VALUES (:title, :uuid, :productionYear, :released, :imdb, :length, :remake, :pca, :createdAt, :updatedAt)";
//        $rsl = $psql->prepare($sql);
//        $rsl->execute([
//            // set correct values
//            'title' => ($film['title']) ? $film['title'] : null,
//            'productionYear' => ($film['productionyear']) ? $film['productionyear'] : null,
//            'released' => ($film['released']) ? $film['released'] : null,
//            'imdb' => ($film['id_imdb']) ? $film['id_imdb'] : null,
//            'length' => ($film['length']) ? $film['length'] : null,
//            'remake' => ($film['remake']) ? $film['remake'] : null,
//            'pca' => ($film['pca_texte']) ? $film['pca_texte'] : null,
//            'createdAt' => $basics['date'],
//            'updatedAt' => $basics['date'],
//            'uuid' => $basics['uuid']
//        ]);

        dd($itemsList);
    }

}
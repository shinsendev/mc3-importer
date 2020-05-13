<?php

declare(strict_types=1);

namespace App\Component\Migration;

use App\Component\MySQL\Connection\MySQLConnection;
use App\Component\PostgreSQL\Connection\PostgreSQLConnection;
use Ramsey\Uuid\Uuid;

class ImportFilms
{
    /**
     * @param $psql
     * @param array $film
     */
    static public function insert($psql, array $film, $mysql):void
    {
        $uuid = Uuid::uuid4()->toString();
        $date = new \DateTime();
        $date = $date->format('Y-m-d H:i:s');

        $sql = "INSERT INTO film (title, uuid, production_year, released_year, imdb, length, remake, pca, created_at, updated_at) VALUES (:title, :uuid, :productionYear, :released, :imdb, :length, :remake, :pca, :createdAt, :updatedAt)";
        $rsl = $psql->prepare($sql);
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

        // add films attributes
        if ($film['adapatation']) {
            //todo : refacto into helper

            // select corresponding mysql thesaurus
            $stm = $mysql->prepare('SELECT * FROM thesaurus WHERE thesaurus_id = :thesaurusId');
            $stm->execute(['thesaurusId' => $film['adapatation']]);
            $thesaurus = $stm->fetch();

            // find corresponding postgres category
            $categoryId = MigrationHelper::getCategoryId($thesaurus, $psql, $mysql);

            // find corresponding postgres attribute
            $stm = $psql->prepare('SELECT * FROM attribute WHERE title = :title AND category_id = :category');
            $stm->execute([
                'title' => $thesaurus['title'],
                'category' => $categoryId
            ]);
            $attribute = $stm->fetch();

            // get film id of the last film inserted
            $stm = $psql->prepare('SELECT LASTVAL() as item_id');
            $stm->execute();
            $lastItemId = $stm->fetch()['item_id'];

            // insert attribute into psql film_attribute table
            $stm = $psql->prepare('INSERT INTO film_attribute (film_id, attribute_id) VALUES (:item, :attribute)');
            $stm->execute([
                'item' => $lastItemId,
                'attribute' => $attribute['id'],
            ]);
        }

        if ($film['verdict']) {

        }

        if ($film['legion']) {

        }

        if ($film['protestant']) {

        }

        if ($film['harrisson']) {

        }

        if ($film['bord']) {

        }

    }
}
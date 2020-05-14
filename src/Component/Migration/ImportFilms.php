<?php

declare(strict_types=1);

namespace App\Component\Migration;

use App\Component\Migration\Helper\AttributeHelper;
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

        // add films attributes (todo: create an attribute list and refacto with a foreach?)
        if ($film['adapatation']) {
            AttributeHelper::importAttribute($film['adapatation'], 'adaptation', 'film', $psql, $mysql);
        }

        if ($film['verdict']) {
            AttributeHelper::importAttribute($film['verdict'], 'verdict', 'film', $psql, $mysql);
        }

        if ($film['legion']) {
            AttributeHelper::importAttribute($film['legion'], 'legion', 'film', $psql, $mysql);
        }

        if ($film['protestant']) {
            AttributeHelper::importAttribute($film['protestant'], 'protestant', 'film', $psql, $mysql);
        }

        if ($film['harrisson']) {
            AttributeHelper::importAttribute($film['harrisson'], 'harrison', 'film', $psql, $mysql);
        }

        if ($film['bord']) {
            AttributeHelper::importAttribute($film['bord'], 'board', 'film', $psql, $mysql);
        }

        //todo:import links with persons + state & censorship
    }
}
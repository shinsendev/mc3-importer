<?php

declare(strict_types=1);

namespace App\Component\Migration;

use App\Component\Migration\Helper\AttributeHelper;
use App\Component\Migration\Helper\MigrationHelper;
use App\Component\Migration\Helper\PersonHelper;
use Ramsey\Uuid\Uuid;

class ImportFilms implements ImporterInterface
{
    CONST MODEL = 'film';

    /**
     * @param $pgsql
     * @param array $film
     */
    static public function insert($pgsql, array $film, $mysql):void
    {
        $basics = MigrationHelper::createBaseParams();

        $sql = "INSERT INTO film (title, uuid, production_year, released_year, imdb, length, remake, pca, created_at, updated_at, mysql_id) VALUES (:title, :uuid, :productionYear, :released, :imdb, :length, :remake, :pca, :createdAt, :updatedAt, :mysqlId)";
        $rsl = $pgsql->prepare($sql);
        $rsl->execute([
            // set correct values
            'title' => ($film['title']) ? $film['title'] : null,
            'productionYear' => ($film['productionyear']) ? $film['productionyear'] : null,
            'released' => ($film['released']) ? $film['released'] : null,
            'imdb' => ($film['id_imdb']) ? $film['id_imdb'] : null,
            'length' => ($film['length']) ? $film['length'] : null,
            'remake' => ($film['remake']) ? $film['remake'] : null,
            'pca' => ($film['pca_texte']) ? $film['pca_texte'] : null,
            'createdAt' => $basics['date'],
            'updatedAt' => $basics['date'],
            'uuid' => $basics['uuid'],
            'mysqlId' => $film['film_id'],
        ]);

        // add films attributes (todo: create an attribute list and refacto with a foreach?)
        if ($film['adapatation']) {
            AttributeHelper::importAttribute($film['adapatation'], 'adaptation', 'film', $pgsql, $mysql);
        }

        if ($film['verdict']) {
            AttributeHelper::importAttribute($film['verdict'], 'verdict', 'film', $pgsql, $mysql);
        }

        if ($film['legion']) {
            AttributeHelper::importAttribute($film['legion'], 'legion', 'film', $pgsql, $mysql);
        }

        if ($film['protestant']) {
            AttributeHelper::importAttribute($film['protestant'], 'protestant', 'film', $pgsql, $mysql);
        }

        if ($film['harrisson']) {
            AttributeHelper::importAttribute($film['harrisson'], 'harrison', 'film', $pgsql, $mysql);
        }

        if ($film['bord']) {
            AttributeHelper::importAttribute($film['bord'], 'board', 'film', $pgsql, $mysql);
        }

        // add persons relations, first we prepare the params

        $personParams['date'] = $basics['date'];
        $personParams['uuid'] = $basics['uuid'];
        $personParams['targetType'] =  self::MODEL;
        $personParams['targetMySQLId'] =  $film['film_id'];

        // add directors
        PersonHelper::importLinkedPersons('film_has_person', 'director', $pgsql,  $mysql, $personParams);

        // add producers
        PersonHelper::importLinkedPersons('film_has_producer', 'producer', $pgsql,  $mysql, $personParams);


        // todo:import links state & censorship

        // todo: import users
    }
}
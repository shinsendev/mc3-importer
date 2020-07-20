<?php

declare(strict_types=1);

namespace App\Component\Migration;

use App\Component\Migration\Helper\AttributeHelper;
use App\Component\Migration\Helper\MigrationHelper;
use App\Component\Migration\Helper\PersonHelper;
use App\Component\Migration\Helper\UserHelper;

class ImportFilms implements ImporterInterface
{
    CONST MODEL = 'film';

    /**
     * @param \PDO $pgsql
     * @param array $film
     * @param \PDO $mysql
     * @param array $params
     */
    static public function insert($pgsql, array $film, $mysql, $params = []):void
    {
        $basics = MigrationHelper::createBaseParams();
        $sql = "INSERT INTO film (title, uuid, production_year, released_year, imdb, length, remake, sample, pca, created_at, updated_at, mysql_id) VALUES (:title, :uuid, :productionYear, :released, :imdb, :length, :remake, :sample, :pca, :createdAt, :updatedAt, :mysqlId)";
        $rsl = $pgsql->prepare($sql);
        $rsl->execute([
            // set correct values
            'title' => ($film['title']) ? $film['title'] : null,
            'productionYear' => ($film['productionyear']) ? $film['productionyear'] : null,
            'released' => ($film['released']) ? $film['released'] : null,
            'imdb' => ($film['id_imdb']) ? $film['id_imdb'] : null,
            'length' => ($film['length']) ? $film['length'] : null,
            'remake' => (isset($film['remake'])) ? MigrationHelper::getBoolValue($film['remake']) : null, // use isset because if not, PHP considers 0 even in string as not existent when use if ($film['remake'])
            'sample' => (isset($film['sample'])) ? MigrationHelper::getBoolValue($film['sample']) : null,
            'pca' => ($film['pca_texte']) ? $film['pca_texte'] : null,
            'createdAt' => $basics['date'],
            'updatedAt' => $basics['date'],
            'uuid' => $basics['uuid'],
            'mysqlId' => $film['film_id'],
        ]);

        // add films attributes
        $filmAttributesList = [
            'adapatation' => 'adaptation',
            'verdict' => 'verdict',
            'legion' => 'legion',
            'protestant' => 'protestant',
            'harrisson' => 'harrison',
            'bord' => 'board',
        ];

        // import film attributes
        foreach ($filmAttributesList as $index => $type) {
            if ($value = $film[$index]) {
                AttributeHelper::importAttribute($value, $type, 'film', $pgsql, $mysql);
            }
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

        // import users
        UserHelper::importLinkedUsers('film_has_editor', 'film', 'film', $pgsql, $mysql, $basics, (int)$film['film_id']);
    }


}
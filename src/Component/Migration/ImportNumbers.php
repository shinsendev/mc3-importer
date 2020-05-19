<?php

declare(strict_types=1);

namespace App\Component\Migration;

use App\Component\Migration\Helper\AttributeHelper;
use App\Component\Migration\Helper\FilmHelper;
use App\Component\Migration\Helper\MigrationHelper;
use App\Component\Migration\Helper\PersonHelper;

/**
 * Class ImportNumbers
 * @package App\Component\Migration
 */
class ImportNumbers implements ImporterInterface
{
    CONST MODEL = 'number';

    /**
     * @param \PDO $pgsql
     * @param array $number
     * @param \PDO $mysql
     * @param array $params
     */
    static public function insert(\PDO $pgsql, array $number, \PDO $mysql, $params = []):void
    {
        $basics = MigrationHelper::createBaseParams();

        // get Film id by using sql Film id
        // use $number['film_id']`
        $filmId = FilmHelper::findFilmByMsqlId((int)$number['film_id'], $pgsql, $mysql);

        $sql = "INSERT INTO number (title, film_id, begin_tc, end_tc, shots, \"references\", uuid, created_at, updated_at, mysql_id) VALUES (:title, :film, :begin, :end, :shots, :references, :uuid, :createdAt, :updatedAt, :mysqlId)";
        $rsl = $pgsql->prepare($sql);
        $rsl->execute([
            'title' => ($number['title']) ? $number['title'] : null,
            'film' => $filmId,
            'begin' => ($number['begin_tc']) ? $number['begin_tc'] : 0,
            'end' => ($number['end_tc']) ? $number['end_tc'] : 0,
            'shots' => ($number['shots']) ? $number['shots'] : null,
            'references' => ($number['quotation_text']) ? $number['quotation_text'] : null,
            'createdAt' => ($number['date_creation'] && $number['last_update'] > 0) ? $number['date_creation'] : $basics['date'],
            'updatedAt' => ($number['last_update'] && $number['last_update'] > 0) ? $number['last_update'] : $basics['date'],
            'uuid' => $basics['uuid'],
            'mysqlId' => $number['number_id'],
        ]);

        if ($number['dubbing']) {
            AttributeHelper::importAttribute($number['dubbing'], 'dubbing', 'number', $pgsql, $mysql);
        }

        if ($number['structure_id']) {
            AttributeHelper::importAttribute($number['structure_id'], 'structure', 'number', $pgsql, $mysql);
        }

        if ($number['begin_thesaurus']) {
            AttributeHelper::importAttribute($number['begin_thesaurus'], 'begin', 'number', $pgsql, $mysql);
        }

        if ($number['ending_thesaurus']) {
            AttributeHelper::importAttribute($number['ending_thesaurus'], 'ending', 'number', $pgsql, $mysql);
        }

        if ($number['completeness_id']) {
            AttributeHelper::importAttribute($number['completeness_id'], 'completeness', 'number', $pgsql, $mysql);
        }

        if ($number['performance_thesaurus_id']) {
            AttributeHelper::importAttribute($number['performance_thesaurus_id'], 'performance', 'number', $pgsql, $mysql);
        }

        if ($number['spectators_thesaurus_id']) {
            AttributeHelper::importAttribute($number['spectators_thesaurus_id'], 'spectators', 'number', $pgsql, $mysql);
        }

        if ($number['musician_thesaurus_id']) {
            AttributeHelper::importAttribute($number['musician_thesaurus_id'], 'musicians', 'number', $pgsql, $mysql);
        }

        if ($number['tempo_thesaurus']) {
            AttributeHelper::importAttribute($number['tempo_thesaurus'], 'tempo', 'number', $pgsql, $mysql);
        }

        if ($number['cast_id']) {
            AttributeHelper::importAttribute($number['cast_id'], 'cast', 'number', $pgsql, $mysql);
        }

        // add persons relations

        $personParams['date'] = $basics['date'];
        $personParams['uuid'] = $basics['uuid'];
        $personParams['targetType'] =  self::MODEL;
        $personParams['targetMySQLId'] =  $number['number_id'];

        // add arrangers
        PersonHelper::importLinkedPersons('number_has_arranger', 'arranger', $pgsql,  $mysql, $personParams);

        // add choregraphs
        PersonHelper::importLinkedPersons('number_has_choregraph', 'choregraph', $pgsql,  $mysql, $personParams);

        // add directors
        PersonHelper::importLinkedPersons('number_has_director', 'director', $pgsql,  $mysql, $personParams);

        // add figurants
        PersonHelper::importLinkedPersons('number_has_figurant', 'figurant', $pgsql,  $mysql, $personParams);

        // add performers
        PersonHelper::importLinkedPersons('number_has_performer', 'performer', $pgsql,  $mysql, $personParams);
    }

}
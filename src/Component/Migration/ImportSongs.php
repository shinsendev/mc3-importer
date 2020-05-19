<?php

declare(strict_types=1);


namespace App\Component\Migration;

use App\Component\Migration\Helper\MigrationHelper;
use App\Component\Migration\Helper\PersonHelper;

/**
 * Class ImportSong
 * @package App\Component\Migration
 */
class ImportSongs implements ImporterInterface
{
    CONST MODEL = 'song';

    static public function insert(\PDO $pgsql, array $song, \PDO $mysql)
    {
        $basics = MigrationHelper::createBaseParams();

        $sql = "INSERT INTO song (title, uuid, year, created_at, updated_at, mysql_id) VALUES (:title, :uuid, :year, :createdAt, :updatedAt, :mysqlId)";
        $rsl = $pgsql->prepare($sql);
        $rsl->execute([
            // set correct values
            'title' => ($song['title']) ? $song['title'] : null,
            'year' => ($song['date'] && $song['date'] > 0) ? $song['date'] : null,
            'createdAt' => ($song['date_creation'] && $song['date_creation'] > 0) ? $song['date_creation'] : $basics['date'],
            'updatedAt' => ($song['last_update'] && $song['last_update'] > 0) ? $song['last_update'] : $basics['date'],
            'uuid' => $basics['uuid'],
            'mysqlId' => $song['song_id'],
        ]);

        // add persons relations, first we prepare the params
        $personParams['date'] = $basics['date'];
        $personParams['uuid'] = $basics['uuid'];
        $personParams['targetType'] =  self::MODEL;
        $personParams['targetMySQLId'] =  $song['song_id'];

        // add composer
        PersonHelper::importLinkedPersons('song_has_composer', 'composer', $pgsql,  $mysql, $personParams);

        // add lyricist
        PersonHelper::importLinkedPersons('song_has_lyricist', 'lyricist', $pgsql,  $mysql, $personParams);
    }

}
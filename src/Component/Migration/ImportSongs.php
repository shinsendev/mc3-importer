<?php

declare(strict_types=1);


namespace App\Component\Migration;

use App\Component\Migration\Helper\MigrationHelper;

/**
 * Class ImportSong
 * @package App\Component\Migration
 */
class ImportSongs implements ImporterInterface
{
    static public function insert(\PDO $pgsql, array $song, \PDO $mysql)
    {
        $basics = MigrationHelper::createBaseParams();

        $sql = "INSERT INTO song (title, uuid, year, created_at, updated_at) VALUES (:title, :uuid, :year, :createdAt, :updatedAt)";
        $rsl = $pgsql->prepare($sql);
        $rsl->execute([
            // set correct values
            'title' => ($song['title']) ? $song['title'] : null,
            'year' => ($song['date'] && $song['date'] > 0) ? $song['date'] : null,
            'createdAt' => ($song['date_creation'] && $song['date_creation'] > 0) ? $song['date_creation'] : $basics['date'],
            'updatedAt' => ($song['last_update'] && $song['last_update'] > 0) ? $song['last_update'] : $basics['date'],
            'uuid' => $basics['uuid']
        ]);
    }

}
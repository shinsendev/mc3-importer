<?php

declare(strict_types=1);

namespace App\Component\Migration;

use App\Component\Migration\Helper\FilmHelper;
use App\Component\Migration\Helper\MigrationHelper;

/**
 * Class ImportNumbers
 * @package App\Component\Migration
 */
class ImportNumbers implements ImporterInterface
{
    static public function insert(\PDO $pgsql, array $number, \PDO $mysql)
    {
        $basics = MigrationHelper::createBaseParams();

        // get Film id by using sql Film id
        // use $number['film_id']
        $filmId = FilmHelper::findFilmByMsqlId((int)$number['film_id'], $pgsql, $mysql);

        $sql = "INSERT INTO number (title, film_id, begin_tc, end_tc, shots, quotation, uuid, created_at, updated_at) VALUES (:title, :film, :begin, :end, :shots, :quotation, :uuid, :createdAt, :updatedAt)";
        $rsl = $pgsql->prepare($sql);
        $rsl->execute([
            'title' => ($number['title']) ? $number['title'] : null,
            'film' => $filmId,
            'begin' => ($number['begin_tc']) ? $number['begin_tc'] : 0,
            'end' => ($number['end_tc']) ? $number['end_tc'] : 0,
            'shots' => ($number['shots']) ? $number['shots'] : null,
            'quotation' => ($number['quotation_text']) ? $number['quotation_text'] : null,
            'createdAt' => ($number['date_creation'] && $number['last_update'] > 0) ? $number['date_creation'] : $basics['date'],
            'updatedAt' => ($number['last_update'] && $number['last_update'] > 0) ? $number['last_update'] : $basics['date'],
            'uuid' => $basics['uuid']
        ]);
    }

}
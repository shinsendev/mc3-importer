<?php

declare(strict_types=1);


namespace App\Component\Migration\Helper;


class FilmHelper
{
    public static function findFilmByMsqlId(int $filmId, \PDO $pgsql, \PDO $mysql):int
    {
        // get mysql film by title and released
        $sql = 'SELECT title, released FROM film WHERE film_id = :id';
        $stm = $mysql->prepare($sql);
        $stm->execute([
                'id' => $filmId
        ]);

        $film = $stm->fetch();

        // get psql film with title and released
        $stm = $pgsql->prepare('SELECT * FROM film WHERE title = :title AND released_year = :released');
        $stm->execute([
            'title' => $film['title'],
            'released' => $film['released'],
        ]);

        return $stm->fetch()['id'];
    }
}
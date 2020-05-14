<?php

declare(strict_types=1);


namespace App\Component\Migration\Helper;


class CategoryHelper
{
    /**
     * @param $thesaurus
     * @param \PDO $psql
     * @param \PDO $mysql
     * @return int|null
     */
    static public function getCategoryId($thesaurus, \PDO $psql, \PDO $mysql):?int
    {
        // get the correct category id and add it in the insert
        // get code content = the name in MySQL db
        $rsl = $mysql->prepare('SELECT * FROM code WHERE code_id = :code');
        $rsl->execute(['code' => $thesaurus['code_id']]);
        $code = $rsl->fetch()['content'];

        // get the category id in PostgreSQL db
        $rsl = $psql->prepare('SELECT id FROM category WHERE code = :code');
        $rsl->execute(['code' => $code]);

        // return psql category id
        return $rsl->fetch()['id'];
    }
}
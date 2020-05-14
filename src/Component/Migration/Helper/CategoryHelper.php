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
    static public function getExistingCategory(string $codeId, \PDO $psql, \PDO $mysql):?int
    {
        // get code content = the name in MySQL db
        $rsl = $mysql->prepare('SELECT * FROM code WHERE code_id = :code');
        $rsl->execute(['code' => $codeId]);
        $code = $rsl->fetch()['content'];

        // get the category id in PostgreSQL db
        $rsl = $psql->prepare('SELECT id FROM category WHERE code = :code');
        $rsl->execute(['code' => $code]);

        // return psql category id
        return $rsl->fetch()['id'];
    }

    static public function getCategory(string $categoryTitle, string $model, \PDO $psql, \PDO $mysql):int
    {
        //todo : add model type
        // try to find an existing category
        $rsl = $psql->prepare('SELECT id FROM category WHERE title = :title');
        $rsl->execute([
            'title' => $categoryTitle
        ]);
        dd($rsl->fetch());
        $categoryId = 0;
        // if not exists create a new one

        return $categoryId;
    }
}
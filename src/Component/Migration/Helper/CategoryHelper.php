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
        // try to find an existing category
        $rsl = $psql->prepare('SELECT id FROM category WHERE title = :title AND model = :model');
        $rsl->execute([
            'title' => $categoryTitle,
            'model' => $model,
        ]);

        // if we find a category, cool, we return it
        if ($category = $rsl->fetch()) {
            return $category;
        }

        // else we have to create a new one

        $categoryId = 0;
        // if not exists create a new one

        return $categoryId;
    }

    static public function createCategory(array $params, \PDO $pgsql)
    {
        $sql = "INSERT INTO category (title, code, description, uuid, created_at, updated_at) VALUES (:title, :code, :description, :uuid, :createdAt, :updatedAt)";
        $rsl = $pgsql->prepare($sql);
        $rsl->execute($params);
    }

}
<?php

declare(strict_types=1);

namespace App\Component\Migration\Helper;

/**
 * Class CategoryHelper
 * @package App\Component\Migration\Helper
 */
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

    /**
     * @param string $categoryTitle
     * @param string $model
     * @param \PDO $pgsql
     * @return string|null
     */
    static public function getCategory(string $categoryTitle, string $model, \PDO $pgsql):?int
    {
        // try to find an existing category
        $rsl = $pgsql->prepare('SELECT id FROM category WHERE title = :title AND model = :model');
        $rsl->execute([
            'title' => $categoryTitle,
            'model' => $model,
        ]);

        // if we find a category, cool, we return it
        if ($category = $rsl->fetch()) {
            return $category['id'];
        }

        return null;
    }

    /**
     * @param string $categoryTitle
     * @param string $model
     * @param \PDO $pgsql
     * @return int
     */
    static public function createCategory(string $categoryTitle, string $model, \PDO $pgsql):int
    {
        // try to find an existing category, if we find a category, cool, we return it
        if ($category = self::getCategory($categoryTitle, $model, $pgsql)) {
            // if we get an array we return the result with index id
            if (gettype($category) === 'array') {
                $category = $category['id'];
            }
            return $category;
        }

        // else we have to create a new one
        $basics = MigrationHelper::createBaseParams();

        $params = [
            // set correct values
            'title' => $categoryTitle,
            'code' => $categoryTitle,
            'model' => $model,
            'description' => null,
            'createdAt' => $basics['date'],
            'updatedAt' => $basics['date'],
            'uuid' => $basics['uuid']
        ];
        self::insertCategory($params, $pgsql);

        // and we eventually return the last id of the category created
        $stm = $pgsql->prepare('  SELECT currval(pg_get_serial_sequence(\'category\',\'id\')) as category_id');
        $stm->execute();

        return $stm->fetch()['category_id'];
    }

    static public function insertCategory(array $params, \PDO $pgsql)
    {
        $sql = "INSERT INTO category (title, code, description, model, uuid, created_at, updated_at) VALUES (:title, :code, :description, :model, :uuid, :createdAt, :updatedAt)";
        $rsl = $pgsql->prepare($sql);
        $rsl->execute($params);
    }

}
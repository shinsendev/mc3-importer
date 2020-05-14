<?php

declare(strict_types=1);


namespace App\Component\Migration;

use Ramsey\Uuid\Uuid;

class ImportAttributes implements ImporterInterface
{
    static public function insert($psql, $thesaurus, $mysql):void
    {
        $uuid = Uuid::uuid4()->toString();
        $date = new \DateTime();
        $date = $date->format('Y-m-d H:i:s');

        // get the correct category id and add it in the insert
        // get code content = the name in MySQL db

        //todo: replace with helper function
        $rsl = $mysql->prepare('SELECT * FROM code WHERE code_id = :code');
        $rsl->execute(['code' => $thesaurus['code_id']]);
        $code = $rsl->fetch()['content'];

        // get the category id in PostgreSQL db
        $rsl = $psql->prepare('SELECT id FROM category WHERE code = :code');
        $rsl->execute(['code' => $code]);
        $categoryId = $rsl->fetch()['id'];

        // set correct values for execute
        $params = [
            'title' => ($thesaurus['title']) ? $thesaurus['title'] : null,
            'definition' => ($thesaurus['definition']) ? $thesaurus['definition'] : null,
            'example' => ($thesaurus['example']) ? $thesaurus['example'] : null,
            'createdAt' => ($thesaurus['date_creation'] && $thesaurus['date_creation'] > 0 ) ? $thesaurus['date_creation'] : $date,
            'updatedAt' => ($thesaurus['last_update'] && $thesaurus['date_creation'] > 0) ? $thesaurus['last_update'] : $date,
            'uuid' => $uuid,
            'categoryId' => $categoryId,
        ];

        // insert data into PostgreSQL attribute table
        self::saveAttribute($params, $psql);

        //todo : add comment
    }

    public static function saveAttribute(array $params, \PDO $psql)
    {
        $sql = "INSERT INTO attribute (title, description, example, uuid, created_at, updated_at, category_id) VALUES (:title, :definition, :example, :uuid, :createdAt, :updatedAt, :categoryId)";
        $rsl = $psql->prepare($sql);
        $rsl->execute($params);
    }
}
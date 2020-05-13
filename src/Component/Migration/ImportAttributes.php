<?php

declare(strict_types=1);


namespace App\Component\Migration;


use App\Component\MySQL\Connection\MySQLConnection;
use Ramsey\Uuid\Uuid;

class ImportAttributes
{
    static public function insert($connection, $thesaurus):void
    {
        $uuid = Uuid::uuid4()->toString();
        $date = new \DateTime();
        $date = $date->format('Y-m-d H:i:s');

        // get the correct category id and add it in the insert
        // get code content = the name in MySQL db
        $mysql = MySQLConnection::connection();
        $rsl = $mysql->prepare('SELECT * FROM code WHERE code_id = :code');
        $rsl->execute(['code' => $thesaurus['code_id']]);
        $code = $rsl->fetch()['content'];

        // get the category id in PostgreSQL db
        $rsl = $connection->prepare('SELECT id FROM category WHERE code = :code');
        $rsl->execute(['code' => $code]);
        $categoryId = $rsl->fetch()['id'];

        // insert MySQL data into PostgreSQL
        $sql = "INSERT INTO attribute (title, description, example, uuid, created_at, updated_at, category_id) VALUES (:title, :definition, :example, :uuid, :createdAt, :updatedAt, :categoryId)";
        $rsl = $connection->prepare($sql);
        $rsl->execute([
            // set correct values
            'title' => ($thesaurus['title']) ? $thesaurus['title'] : null,
            'definition' => ($thesaurus['definition']) ? $thesaurus['definition'] : null,
            'example' => ($thesaurus['example']) ? $thesaurus['example'] : null,
            'createdAt' => ($thesaurus['date_creation'] && $thesaurus['date_creation'] > 0 ) ? $thesaurus['date_creation'] : $date,
            'updatedAt' => ($thesaurus['last_update'] && $thesaurus['date_creation'] > 0) ? $thesaurus['last_update'] : $date,
            'uuid' => $uuid,
            'categoryId' => $categoryId,
        ]);

        //todo : add comment
    }
}
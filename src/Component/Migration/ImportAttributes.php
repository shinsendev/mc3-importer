<?php

declare(strict_types=1);


namespace App\Component\Migration;


use Ramsey\Uuid\Uuid;

class ImportAttributes
{
    static public function insert($connection, $thesaurus):void
    {
        $uuid = Uuid::uuid4()->toString();
        $date = new \DateTime();
        $date = $date->format('Y-m-d H:i:s');

        // todo: add category

        $sql = "INSERT INTO attribute (title, description, example, uuid, created_at, updated_at) VALUES (:title, :definition, :example, :uuid, :createdAt, :updatedAt)";
        $rsl = $connection->prepare($sql);
        $rsl->execute([
            // set correct values
            'title' => ($thesaurus['title']) ? $thesaurus['title'] : null,
            'definition' => ($thesaurus['definition']) ? $thesaurus['definition'] : null,
            'example' => ($thesaurus['example']) ? $thesaurus['example'] : null,
            'createdAt' => ($thesaurus['date_creation'] && $thesaurus['date_creation'] > 0 ) ? $thesaurus['date_creation'] : $date,
            'updatedAt' => ($thesaurus['last_update'] && $thesaurus['date_creation'] > 0) ? $thesaurus['last_update'] : $date,
            'uuid' => $uuid
        ]);

        //todo : add comment
    }
}
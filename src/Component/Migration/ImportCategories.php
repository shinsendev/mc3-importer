<?php

declare(strict_types=1);

namespace App\Component\Migration;

use Ramsey\Uuid\Uuid;

/**
 * Class ImportCategories
 * @package App\Component\Migration
 */
class ImportCategories
{
    static public function insert($connection, $code):void
    {
        $uuid = Uuid::uuid4()->toString();
        $date = new \DateTime();
        $date = $date->format('Y-m-d H:i:s');

        $sql = "INSERT INTO category (title, code, description, uuid, created_at, updated_at) VALUES (:title, :code, :description, :uuid, :createdAt, :updatedAt)";
        $rsl = $connection->prepare($sql);
        $rsl->execute([
            // set correct values
            'title' => ($code['title']) ? $code['title'] : null,
            'code' => ($code['content']) ? $code['content'] : null,
            'description' => ($code['description']) ? $code['description'] : null,
            'createdAt' => ($code['date_creation']) ? $code['date_creation'] : $date,
            'updatedAt' => ($code['last_update']) ? $code['last_update'] : $date,
            'uuid' => $uuid
        ]);
    }
}
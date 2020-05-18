<?php

declare(strict_types=1);


namespace App\Component\Migration;


use App\Component\Migration\Helper\MigrationHelper;

class ImportStudios implements ImporterInterface
{
    /**
     * @param \PDO $pgsql
     * @param array $studio
     * @param \PDO $mysql
     */
    static public function insert(\PDO $pgsql, array $studio, \PDO $mysql):void
    {
        MigrationHelper::savePgSQL($pgsql, self::writeSQL(), self::configure($studio));
    }

    /**
     * @return string
     */
    static public function writeSQL():string
    {
        return "INSERT INTO studio (name, uuid, created_at, updated_at) VALUES (:name, :uuid, :createdAt, :updatedAt)";
    }

    /**
     * @param array $studio
     * @return array
     */
    static public function configure(array $studio):array
    {
        $basics = MigrationHelper::createBaseParams();

        return [
            // set correct values
            'name' => $studio['title'],
            'createdAt' => ($studio['date_creation'] && $studio['date_creation'] > 0) ? $studio['date_creation'] : $basics['date'],
            'updatedAt' => ($studio['last_update'] && $studio['last_update'] > 0) ? $studio['last_update'] : $basics['date'],
            'uuid' => $basics['uuid']
        ];
    }

}
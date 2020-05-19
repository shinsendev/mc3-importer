<?php

declare(strict_types=1);


namespace App\Component\Migration;


use App\Component\Migration\Helper\MigrationHelper;

class ImportDistributors implements ImporterInterface
{
    static public function insert(\PDO $pgsql, array $distributor, \PDO $mysql):void
    {
        MigrationHelper::savePgSQL($pgsql, self::writeSQL(), self::configure($distributor));
    }
    /**
     * @return string
     */
    static public function writeSQL():string
    {
        return "INSERT INTO distributor (name, uuid, created_at, updated_at, mysql_id) VALUES (:name, :uuid, :createdAt, :updatedAt, :mysqlId)";
    }

    /**
     * @param array $studio
     * @return array
     */
    static public function configure(array $distributor):array
    {
        $basics = MigrationHelper::createBaseParams();

        return [
            // set correct values
            'name' => $distributor['title'],
            'createdAt' => ($distributor['date_creation'] && $distributor['date_creation'] > 0) ? $distributor['date_creation'] : $basics['date'],
            'updatedAt' => ($distributor['last_update'] && $distributor['last_update'] > 0) ? $distributor['last_update'] : $basics['date'],
            'uuid' => $basics['uuid'],
            'mysqlId' =>  $distributor['distributor_id']
        ];
    }

}
<?php

declare(strict_types=1);

namespace App\Component\Migration;


class ImportRelations implements ImporterInterface
{
    static public function insert(\PDO $pgsql, array $item, \PDO $mysql, array $params):void
    {
        $tableName = $params['tableName'];
        dd($item);

        $sql = "INSERT INTO '.$tableName.' VALUES (:sourceId, :targetId)";
        $rsl = $pgsql->prepare($sql);
        $rsl->execute([
            'sourceId' => '',
            'targetId' => '',
        ]);



//        $sql = 'INSERT INTO '.$pgSQLTableName.' VALUES(:source, :target)';
//        $stmt = $pgsql->prepare($sql);
//
//        $sourceIdName = $source.'_id';
//        $sourceId = MigrationHelper::getEntityByMySQLId($pgsql, $relation[$sourceIdName], $source);
//
//        $targetIdName = $target.'_id';
//        $targetId = MigrationHelper::getEntityByMySQLId($pgsql, $relation[$targetIdName], $target);
//
//        $stmt->execute(['source' => $sourceId, 'target' => $targetId]);
    }

}
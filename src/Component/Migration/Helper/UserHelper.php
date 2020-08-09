<?php

declare(strict_types=1);


namespace App\Component\Migration\Helper;


class UserHelper
{
    public static function importLinkedUsers(
        string $mySQLTable,
        string $pgSQLTable,
        string $model,
        \PDO $pgsql,
        \PDO $mysql,
        array $basics,
        int $mysqlItemId):void
    {
        $usersParams['date'] = $basics['date'];
        $usersParams['uuid'] = $basics['uuid'];
        $usersParams['id'] = $mysqlItemId;

        // get last item
        $stm = $pgsql->prepare('SELECT currval(pg_get_serial_sequence(\''.$pgSQLTable.'\',\'id\')) as id');
        $stm->execute();
        $itemIdToUpdate = $stm->fetch()['id'];

        $sql = 'SELECT * FROM '.$mySQLTable.' WHERE  '.$model.'_id = :id';
        $stm = $mysql->prepare($sql);
        $stm->execute([
            'id' => $mysqlItemId
        ]);
        $relations = $stm->fetchAll();

        $value = [];
        foreach ($relations as $relation) {
            $user = self::getEditor((int)$relation['editors'], $pgsql);
            $value[] = [
                'user' => $user['name'],
                'email' => $user['email'],
                'uuid' => $user['uuid'],
                'date' => $basics['date'],
            ];
        }

        // update item with the data
        $sql = 'UPDATE '.$pgSQLTable.' SET contributors = :value WHERE id = :id';
        $stm = $pgsql->prepare($sql);
        $stm->execute([
            'id' => $itemIdToUpdate,
            'value' => json_encode($value)
        ]);
    }

    public static function getEditor(int $id, \PDO $pgsql):array
    {
        $sql = 'SELECT * FROM contributor WHERE mysql_id = :id';
        $stm = $pgsql->prepare($sql);
        $stm->execute(['id' => $id]);

        return $stm->fetch();
    }
}
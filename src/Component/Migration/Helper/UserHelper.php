<?php

declare(strict_types=1);


namespace App\Component\Migration\Helper;


class UserHelper
{
    public static function importLinkedUsers(string $mySQLTable, string $pgSQLTable, int $itemIdToUpdate, array $params, \PDO $pgsql, \PDO $mysql, array $basics):void
    {
        $sql = 'SELECT * FROM '.$mySQLTable.' WHERE  film_id = :filmId';
        $stm = $mysql->prepare($sql);
        $stm->execute([
            'filmId' => $params['filmId']
        ]);
        $relations = $stm->fetchAll();

        $value = [];
        foreach ($relations as $relation) {
            $user = self::getEditor((int)$relation['editors'], $pgsql);
            $value[] = [
                'user' => $user['name'],
                'email' => $user['email'],
                'uuii' => $user['uuid'],
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

    public static function getEditor(int $id, \PDO $pgsql)
    {
        $sql = 'SELECT * FROM "user" WHERE mysql_id = :id';
        $stm = $pgsql->prepare($sql);
        $stm->execute(['id' => $id]);

        return $stm->fetch();
    }
}
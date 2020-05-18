<?php

declare(strict_types=1);


namespace App\Component\Migration\Helper;


class PersonHelper
{
    static function importLinkedPersons(string $tableName, string $profession, \PDO $pgsql, \PDO $mysql, array $params)
    {
        // first we check in the mysql relation table if there is a link with our entity

        $sql = 'SELECT * FROM '.$tableName.' WHERE '.$params['targetType'].'_id = :mysqlId';
        $rsl = $mysql->prepare($sql);
        $rsl->execute([
            'mysqlId' => $params['targetMySQLId']
        ]);
        $results = $rsl->fetchAll();

        // if we have some results we save it
        if ($results) {
            foreach ($results as $result) {
                // get person id in pgsql DB using the mysql_id
                $sql = "SELECT id FROM person WHERE mysql_id = :mysqlId";
                $rsl = $pgsql->prepare($sql);
                $rsl->execute([
                    'mysqlId' => $result['person_id']
                ]);
                $personId = $rsl->fetch()['id'];

                if(!$personId) {
                    // if there is no result, it's a MysSQL DB error, we can skip the association
                    return;
                }

                $sql = "INSERT INTO work (person_id, target_uuid, target_type, created_at, profession) VALUES (:personId, :targetUuid, :targetType, :createdAt, :profession)";
                $rsl = $pgsql->prepare($sql);
                $rsl->execute([
                    'personId' => $personId,
                    'targetUuid' => $params['uuid'],
                    'targetType' => $params['targetType'],
                    'createdAt' => $params['date'],
                    'profession' => $profession,
                ]);
            }

        }
    }
}
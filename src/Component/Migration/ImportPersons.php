<?php

declare(strict_types=1);


namespace App\Component\Migration;


use App\Component\Migration\Helper\MigrationHelper;

class ImportPersons implements ImporterInterface
{
    static public function insert(\PDO $pgsql, array $person, \PDO $mysql)
    {
        if ($person['name']) {
            preg_match('/(.*\s)(\S*)/', $person['name'], $matches, PREG_OFFSET_CAPTURE);
            if (!isset($matches[1])) {
                $firstname = $person['name'];
            }
            else {
                if (isset($matches[1])) {
                    $firstname = $matches[1][0];
                }

                if (count($matches)>1) {
                    $lastname = $matches[2][0];
                }
            }
        }

        $basics = MigrationHelper::createBaseParams();

        $sql = "INSERT INTO person (firstname, lastname, gender, uuid, created_at, updated_at) VALUES (:firstname, :lastname, :gender, :uuid, :createdAt, :updatedAt)";
        $rsl = $pgsql->prepare($sql);
        $rsl->execute([
            'firstname' => (isset($firstname)) ? $firstname : null,
            'lastname' => (isset($lastname)) ? $lastname : null,
            'gender' => ($person['gender']) ? $person['gender'] : null,
            'createdAt' => ($person['date_creation'] && $person['date_creation'] > 0) ? $person['date_creation'] : $basics['date'],
            'updatedAt' => ($person['last_update'] && $person['last_update'] > 0) ? $person['last_update'] : $basics['date'],
            'uuid' => $basics['uuid']
        ]);
    }
}
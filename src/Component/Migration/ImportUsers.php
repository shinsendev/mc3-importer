<?php

declare(strict_types=1);


namespace App\Component\Migration;


use App\Component\Migration\Helper\MigrationHelper;

class ImportUsers implements ImporterInterface
{
    static public function insert(\PDO $pgsql, array $user, \PDO $mysql, $params = []):void
    {
        MigrationHelper::savePgSQL($pgsql, self::writeSQL(), self::configure($user));
    }

    /**
     * @return string
     */
    static public function writeSQL():string
    {
        return "INSERT INTO contributor (name, email, uuid, created_at, updated_at, mysql_id) VALUES (:name, :email, :uuid, :createdAt, :updatedAt, :mysqlId)";
    }

    /**
     * @param array $studio
     * @return array
     */
    static public function configure(array $user):array
    {
        $basics = MigrationHelper::createBaseParams();

        return [
            // set correct values
            'name' => ($user['username']) ? $user['username'] : null,
            'email' => ($user['email']) ? $user['email'] : null,
            'createdAt' => ($user['last_login'] && $user['last_login'] > 0) ? $user['last_login'] : $basics['date'],
            'updatedAt' => ($user['last_login'] && $user['last_login'] > 0) ? $user['last_login'] : $basics['date'],
            'uuid' => $basics['uuid'],
            'mysqlId' => $user['id'],
        ];
    }
}
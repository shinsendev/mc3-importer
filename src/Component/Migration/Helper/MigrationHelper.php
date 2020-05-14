<?php

declare(strict_types=1);


namespace App\Component\Migration\Helper;

use App\Component\MySQL\Connection\MySQLConnection;
use App\Component\PostgreSQL\Connection\PostgreSQLConnection;
use Ramsey\Uuid\Uuid;

class MigrationHelper
{
    /**
     * @param string $itemType
     * @param string $insertFunctionName
     * @param int $limit
     */
    static public function importAll(string $itemType, string $insertFunctionName, int $limit = 1000)
    {
        // count number of film
        $mysql = MySQLConnection::connection();
        $sql = 'SELECT COUNT(*) as nb FROM '.$itemType;
        $stmt = $mysql->query($sql);
        $stmt->execute();
        $count = ($stmt->fetch()['nb']);

        // divide count by bulk size
        $iterationsCount = ceil($count/$limit);

        // save all films bulks
        $offset = 0;

        for ($i = 0; $i < $iterationsCount; $i++) {
            MigrationHelper::importBulk($offset, $limit, 'SELECT * FROM '.$itemType.' LIMIT %d, %d', $insertFunctionName);
            $offset = $offset+$limit;
        }

    }

    static public function importBulk($offset, $limit, $sql, $insertFunctionName)
    {
        // connect to MySQL and get films list
        $mysql = MySQLConnection::connection();

        // extract film list in an array (we need to pass int $limit and $offset in a special way for mysql and not as parameters: https://stackoverflow.com/questions/10014147/limit-keyword-on-mysql-with-prepared-statement)
        $sql = sprintf($sql, $offset, $limit);
        $stmt = $mysql->prepare($sql);
        $stmt->execute();
        $items = $stmt->fetchAll();

        // connect to PostgreSQL and insert the usefull data of the list
        $psql = PostgreSQLConnection::connection();

        // the we insert the films one by one
        foreach ($items as $item) {
            $insertFunctionName($psql, $item, $mysql);
        }

        return true;
    }

    static function createBaseParams()
    {
        $params = [];
        $params['uuid'] = Uuid::uuid4()->toString();
        $date = new \DateTime();
        $params['date'] = $date->format('Y-m-d H:i:s');

        return $params;
    }
}
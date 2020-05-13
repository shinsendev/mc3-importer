<?php

declare(strict_types=1);


namespace App\Component\Migration;


use App\Component\MySQL\Connection\MySQLConnection;
use App\Component\PostgreSQL\Connection\PostgreSQLConnection;

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

    static public function getCategoryId($thesaurus, $psql, $mysql)
    {
        // get the correct category id and add it in the insert
        // get code content = the name in MySQL db
        $rsl = $mysql->prepare('SELECT * FROM code WHERE code_id = :code');
        $rsl->execute(['code' => $thesaurus['code_id']]);
        $code = $rsl->fetch()['content'];

        // get the category id in PostgreSQL db
        $rsl = $psql->prepare('SELECT id FROM category WHERE code = :code');
        $rsl->execute(['code' => $code]);

        // return psql category id
        return $rsl->fetch()['id'];
    }

}
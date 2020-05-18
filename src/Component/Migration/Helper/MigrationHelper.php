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

        // connect to PostgreSQL and insert the usefull data of the list
        $pgsql = PostgreSQLConnection::connection();

        // connect to MySQL and get films list
        $mysql = MySQLConnection::connection();

        // we add mysql_id field only if it is not exists (very useful for debug)
        $stm = $pgsql->prepare("SELECT column_name FROM information_schema.columns WHERE table_name='".$itemType."' and column_name='mysql_id'");
        $stm->execute();
        if (!$mysqlIdExists = $stm->fetch()) {
            // add a column into item pgsql table for getting the old mysql id
            MigrationHelper::addMySQLId($pgsql, $itemType);
        }

        for ($i = 0; $i < $iterationsCount; $i++) {
            MigrationHelper::importBulk($offset, $limit, $pgsql, $mysql, 'SELECT * FROM '.$itemType.' LIMIT %d, %d', $insertFunctionName, $itemType);
            $offset = $offset+$limit;
        }

        // remove the mysql_id column (add to final clean function)
//        MigrationHelper::removeSQLid($pgsql, $itemType);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param \PDO $pgsql
     * @param \PDO $mysql
     * @param string $sql
     * @param string $insertFunctionName
     * @return bool
     */
    static public function importBulk(
        int $offset,
        int $limit,
        \PDO $pgsql,
        \PDO $mysql,
        string $sql,
        string $insertFunctionName
    ):bool
    {
        // extract film list in an array (we need to pass int $limit and $offset in a special way for mysql and not as parameters: https://stackoverflow.com/questions/10014147/limit-keyword-on-mysql-with-prepared-statement)
        $sql = sprintf($sql, $offset, $limit);
        $stmt = $mysql->prepare($sql);
        $stmt->execute();
        $items = $stmt->fetchAll();

        // the we insert the films one by one
        foreach ($items as $item) {
            $insertFunctionName($pgsql, $item, $mysql);
        }

        return true;
    }

    /**
     * @return array
     */
    static function createBaseParams()
    {
        $params = [];
        $params['uuid'] = Uuid::uuid4()->toString();
        $date = new \DateTime();
        $params['date'] = $date->format('Y-m-d H:i:s');

        return $params;
    }

    /**
     * @param \PDO $pgsql
     * @param string $sql
     * @param array $params
     */
    static public function savePgSQL(\PDO $pgsql, string $sql, array $params)
    {
        $rsl = $pgsql->prepare($sql);
        $rsl->execute($params);
    }

    /**
     * @param \PDO $pgsql
     * @param string $model
     */
    static public function addMySQLId(\PDO $pgsql, string $model)
    {
        // we remove the field if it exists and we recreate it
        $sql = 'ALTER TABLE '.$model.' ADD mysql_id INT;';
        $rsl = $pgsql->prepare($sql);
        $rsl->execute();
    }

    /**
     * @param \PDO $pgsql
     * @param string $model
     */
    static public function removeSQLid(\PDO $pgsql, string $model)
    {
        // we remove the field if it exists and we recreate it
        $sql = 'ALTER TABLE film DROP mysql_id;';
        $rsl = $pgsql->prepare($sql);
        $rsl->execute();
    }
}
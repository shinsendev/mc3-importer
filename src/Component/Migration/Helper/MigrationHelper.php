<?php

declare(strict_types=1);


namespace App\Component\Migration\Helper;

use App\Component\MySQL\Connection\MySQLConnection;
use App\Component\PostgreSQL\Connection\PostgreSQLConnection;
use Ramsey\Uuid\Uuid;

class MigrationHelper
{
    CONST TRUE = 'true';
    CONST FALSE = 'false';

    /**
     * @param string $tableName
     * @param int $limit
     * @param \PDO $mysql
     * @return float
     */
    static public function countIteration(string $tableName, int $limit, \PDO $mysql):float
    {
        // count number of the items
        $sql = 'SELECT COUNT(*) as nb FROM '.$tableName;
        $stmt = $mysql->query($sql);
        $stmt->execute();
        $count = ($stmt->fetch()['nb']);

        // divide count by bulk size and return the iteration count
        return ceil($count/$limit);
    }

    /**
     * @param string $itemType
     * @param string $insertFunctionName
     * @param int $limit
     */
    static public function importAll(string $itemType, string $insertFunctionName, int $limit = 1000):void
    {
        // connect to PostgreSQL and insert the usefull data of the list
        $pgsql = PostgreSQLConnection::connection();

        // connect to MySQL and get films list
        $mysql = MySQLConnection::connection();

        // divide count by bulk size
        $iterationsCount = self::countIteration($itemType, $limit, $mysql);

        // save all films bulks
        $offset = 0;

       self::manageExceptions($itemType, $pgsql);

        for ($i = 0; $i < $iterationsCount; $i++) {
            MigrationHelper::importBulk($offset, $limit, $pgsql, $mysql, 'SELECT * FROM '.$itemType.' LIMIT %d, %d', $insertFunctionName, $itemType);
            $offset = $offset+$limit;
        }

        // todo: remove the mysql_id column (add to final clean function)
//        MigrationHelper::removeSQLid($pgsql, $itemType);
    }

    /**
     * @param string $mySQLTableName
     * @param string $pgSQLTableName
     * @param string $source
     * @param string $target
     * @param int $limit
     */
    static public function importRelations(string $mySQLTableName, string $pgSQLTableName, string $source, string $target, int $limit = 1000)
    {
        // connect to PostgreSQL and insert the usefull data of the list
        $pgsql = PostgreSQLConnection::connection();
        $mysql = MySQLConnection::connection();

        // count number of iterations for import
        $iterationsCount = self::countIteration($mySQLTableName, $limit, $mysql);

        // save all films bulks
        $offset = 0;

        for ($i = 0; $i < $iterationsCount; $i++) {
            $sql = sprintf('SELECT * FROM '.$mySQLTableName.' LIMIT %d, %d', $offset, $limit);
            $stmt = $mysql->prepare($sql);
            $stmt->execute();
            $relations = $stmt->fetchAll();

            // the we insert the films one by one
            foreach ($relations as $relation) {
                self::insertRelation($pgSQLTableName, $relation, $source, $target, $pgsql);
            }
            $offset = $offset+$limit;
        }
    }

    /**
     * @param string $pgSQLTableName
     * @param array $relation
     * @param string $sourceType
     * @param string $targetType
     * @param \PDO $pgsql
     * @param bool $isThesaurus
     */
    public static function insertRelation(string $pgSQLTableName, array $relation, string $sourceType, string $targetType, \PDO $pgsql, bool $isThesaurus = false) : void
    {
        $sql = 'INSERT INTO '.$pgSQLTableName.' VALUES(:source, :target)';
        $stmt = $pgsql->prepare($sql);

        $sourceIdName = $sourceType.'_id';
        $sourceId = MigrationHelper::getEntityByMySQLId($pgsql, $relation[$sourceIdName], $sourceType);

        // if it is a thesaurus, the target is an attribute
        $targetIdName = $targetType.'_id';
        if ($isThesaurus) {
            $targetType = 'attribute';
        }
        $targetId = MigrationHelper::getEntityByMySQLId($pgsql, $relation[$targetIdName], $targetType);

        // we save the relation with the psql id
        $stmt->execute(['source' => $sourceId, 'target' => $targetId]);
    }

    /**
     * @param string $pgSQLTableName
     * @param array $values
     * @param string $sourceIdName
     * @param string $targetIdName
     * @param string $sourceType
     * @param string $targetType
     * @param \PDO $pgsql
     */
    public static function insertRelationAdvanced(
        string $pgSQLTableName,
        array $values,
        string $sourceIdName,
        string $targetIdName,
        string $sourceType,
        string $targetType,
        \PDO $pgsql
    ) : void
    {
        $sql = 'INSERT INTO '.$pgSQLTableName.' VALUES(:source, :target)';
        $stmt = $pgsql->prepare($sql);
        $sourceId = MigrationHelper::getEntityByMySQLId($pgsql, $values[$sourceIdName], $sourceType);
        $targetId = MigrationHelper::getEntityByMySQLId($pgsql, $values[$targetIdName], $targetType);

        // we save the relation with the psql id
        $stmt->execute(['source' => $sourceId, 'target' => $targetId]);
    }

    /**
     * @param string $itemType
     * @param \PDO $pgsql
     */
    static public function manageExceptions(string $itemType,\PDO $pgsql):void
    {
        // manage exceptions
        if ($itemType === 'fos_user') {
            $mysqlTableName = 'user';
        }
        else if ($itemType === 'code') {
            $mysqlTableName = 'category';
        }
        else if ($itemType === 'thesaurus') {
            $mysqlTableName = 'attribute';
        }
        else {
            $mysqlTableName = $itemType;
        }

        // we add mysql_id field only if it is not exists (very useful for debug)
        $stm = $pgsql->prepare("SELECT column_name FROM information_schema.columns WHERE table_name='".$mysqlTableName."' and column_name='mysql_id'");
        $stm->execute();

        if ((!$mysqlIdExists = $stm->fetch())) {
            if($itemType === 'fos_user') {
                $sql = "ALTER TABLE \"user\" ADD mysql_id INT;";
                $rsl = $pgsql->prepare($sql);
                $rsl->execute();
            }
            else {
                // add a column into item pgsql table for getting the old mysql id
                MigrationHelper::addMySQLId($pgsql, $mysqlTableName);
            }
        }
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
    ):void
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
        $sql = 'ALTER TABLE '.$model.' DROP mysql_id;';
        $rsl = $pgsql->prepare($sql);
        $rsl->execute();
    }

    /**
     * @param \PDO $pgsql
     * @param string $mysqlId
     * @param string $model
     * @return mixed
     */
    static public function getEntityByMySQLId(\PDO $pgsql, string $mysqlId, string $model):int
    {
        $sql = 'SELECT id FROM '.$model.' WHERE mysql_id = :id';
        $stm = $pgsql->prepare($sql);
        $stm->execute(['id' => $mysqlId]);

        return $stm->fetch()['id'];
    }

    /**
     * @param string $value
     * @return string
     */
    public static function getBoolValue($value):string // convert boolean value into string
    {
        if ($value === 1 ) {
            $stringValue = self::TRUE;
        }
        else {
            $stringValue = self::FALSE;
        }



        return $stringValue;
    }
}
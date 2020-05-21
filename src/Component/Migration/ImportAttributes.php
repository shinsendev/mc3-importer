<?php

declare(strict_types=1);


namespace App\Component\Migration;

use App\Component\Migration\Helper\CategoryHelper;
use App\Component\Migration\Helper\MigrationHelper;
use App\Component\Migration\Helper\UserHelper;
use App\Component\MySQL\Connection\MySQLConnection;
use App\Component\PostgreSQL\Connection\PostgreSQLConnection;
use Ramsey\Uuid\Uuid;

class ImportAttributes implements ImporterInterface
{
    static public function insert($pgsql, $thesaurus, $mysql, $params = []):void
    {
        $uuid = Uuid::uuid4()->toString();
        $date = new \DateTime();
        $date = $date->format('Y-m-d H:i:s');

        // get the correct category id and add it in the insert
        // get code content = the name in MySQL db

        //todo: replace with helper function
        $rsl = $mysql->prepare('SELECT * FROM code WHERE code_id = :code');
        $rsl->execute(['code' => $thesaurus['code_id']]);
        $code = $rsl->fetch()['content'];

        // get the category id in PostgreSQL db
        $rsl = $pgsql->prepare('SELECT id FROM category WHERE code = :code');
        $rsl->execute(['code' => $code]);
        $categoryId = $rsl->fetch()['id'];

        // set correct values for execute
        $params = [
            'title' => ($thesaurus['title']) ? $thesaurus['title'] : null,
            'definition' => ($thesaurus['definition']) ? $thesaurus['definition'] : null,
            'example' => ($thesaurus['example']) ? $thesaurus['example'] : null,
            'createdAt' => ($thesaurus['date_creation'] && $thesaurus['date_creation'] > 0 ) ? $thesaurus['date_creation'] : $date,
            'updatedAt' => ($thesaurus['last_update'] && $thesaurus['date_creation'] > 0) ? $thesaurus['last_update'] : $date,
            'uuid' => $uuid,
            'categoryId' => $categoryId,
            'mysqlId' => $thesaurus['thesaurus_id']
        ];

        // insert data into PostgreSQL attribute table
        self::saveAttribute($params, $pgsql);

        //todo : add comment

        // import users
        $basics = MigrationHelper::createBaseParams();
        UserHelper::importLinkedUsers('thesaurus_has_editor', 'attribute', 'thesaurus', $pgsql, $mysql, $basics, (int)$thesaurus['thesaurus_id']);
    }

    /**
     * @param array $params
     * @param \PDO $psql
     */
    public static function saveAttribute(array $params, \PDO $psql):void
    {
        $sql = "INSERT INTO attribute (title, description, example, uuid, created_at, updated_at, category_id, mysql_id) VALUES (:title, :definition, :example, :uuid, :createdAt, :updatedAt, :categoryId, :mysqlId)";
        $rsl = $psql->prepare($sql);
        $rsl->execute($params);
    }

    /**
     * @param string $categoryTitle
     * @param string $mysqlTableName
     * @param string $relationTableName
     * @param string $model
     * @param int $limit
     * @param bool $isThesaurus
     */
    public static function importExternalThesaurusString(
        string $categoryTitle,
        string $mysqlTableName,
        string $mySQLRelationTableName,
        string $pgSQLRelationTableName,
        string $model,
        int $limit = 1000,
        bool $isThesaurus = false
    ) :void
    {
        // connect to PostgreSQL and insert the usefull data of the list
        $pgsql = PostgreSQLConnection::connection();

        // connect to MySQL and get films list
        $mysql = MySQLConnection::connection();

        $basics = MigrationHelper::createBaseParams();

        // create a new category (todo: check if not exists)
        $params = [
            // set correct values
            'title' => $categoryTitle,
            'code' => $categoryTitle,
            'model' => $model,
            'description' => null,
            'createdAt' => $basics['date'],
            'updatedAt' => $basics['date'],
            'uuid' => $basics['uuid']
        ];
        CategoryHelper::insertCategory($params, $pgsql);

        // get the new created categoryId
        $categoryId = CategoryHelper::getCategory($categoryTitle, $model, $pgsql);;

        // we add some pagination for importing attributes
        $iterationsCount = MigrationHelper::countIteration($mysqlTableName, $limit, $mysql);
        $offset = 0;

        // import new attributes in attributes table
        for ($i = 0; $i < $iterationsCount; $i++) {
            $sql = sprintf('SELECT * FROM ' . $mysqlTableName . ' LIMIT %d, %d', $offset, $limit);
            $stmt = $mysql->prepare($sql);
            $stmt->execute();
            $attributes = $stmt->fetchAll();

            // then we create a new attribute one by one
            foreach($attributes as $attribute) {
                $attributeIdName = $categoryTitle.'_id';
                $params = [
                    'title' => ($attribute['title']) ? $attribute['title'] : null,
                    'definition' =>  null,
                    'example' =>  null,
                    'createdAt' => ($attribute['date_creation'] && $attribute['date_creation'] > 0 ) ? $attribute['date_creation'] : $basics['date'],
                    'updatedAt' => ($attribute['last_update'] && $attribute['date_creation'] > 0) ? $attribute['last_update'] : $basics['date'],
                    'uuid' => Uuid::uuid4()->toString(), // we create a new uuid for each attribute
                    'categoryId' => $categoryId,
                    'mysqlId' => $attribute[$attributeIdName]
                ];
                ImportAttributes::saveAttribute($params, $pgsql);
            }
            $offset = $offset + $limit;
        }

        // import all relations
        $iterationsCount = MigrationHelper::countIteration($mySQLRelationTableName, $limit, $mysql);
        $offset = 0;

        // import new attributes in attributes table
        for ($i = 0; $i < $iterationsCount; $i++) {
            $sql = sprintf('SELECT * FROM ' . $mySQLRelationTableName . ' LIMIT %d, %d', $offset, $limit);
            $stmt = $mysql->prepare($sql);
            $stmt->execute();
            $relations = $stmt->fetchAll();

            // the we insert the films one by one
            foreach($relations as $relation) {
                MigrationHelper::insertRelation($pgSQLRelationTableName, $relation, $model, $categoryTitle, $pgsql, $isThesaurus);
            }
            $offset = $offset + $limit;
        }
    }

    /**
     * @param string $mysqlTableName
     * @param string $pgsqlTableName
     * @param string $code
     * @param string $model
     * @param string $targetType
     * @param string $sourceIdName
     * @param string $targetIdName
     * @param int $limit
     */
    public static function importRelationsForExistingAttributes(
        string $mysqlTableName,
        string $pgsqlTableName,
        string $code,
        string $model,
        string $targetType,
        string $sourceIdName,
        string $targetIdName,
        int $limit = 1000)
    {
        // init
        $pgsql = PostgreSQLConnection::connection();
        $mysql = MySQLConnection::connection();
        $basics = MigrationHelper::createBaseParams();

        // we add some pagination for importing attributes
        $iterationsCount = MigrationHelper::countIteration($mysqlTableName, $limit, $mysql);
        $offset = 0;

        // find category by code
        $sql = ('SELECT id FROM  category WHERE code = :code');
        $stm = $pgsql->prepare($sql);
        $stm->execute(['code'=> $code]);
        $categoryId = $stm->fetch()['id']; // useless?

        // import new attributes in attributes table
        for ($i = 0; $i < $iterationsCount; $i++) {
            $sql = sprintf('SELECT * FROM ' . $mysqlTableName . ' LIMIT %d, %d', $offset, $limit);
            $stmt = $mysql->prepare($sql);
            $stmt->execute();
            $relations = $stmt->fetchAll();

            // the we insert the films one by one
            foreach($relations as $relation) {
                MigrationHelper::insertRelationAdvanced($pgsqlTableName, $relation, $sourceIdName, $targetIdName, $model, $targetType, $pgsql);
            }
            $offset = $offset + $limit;
        }
    }
}
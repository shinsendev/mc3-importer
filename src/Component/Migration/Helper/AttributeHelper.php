<?php

declare(strict_types=1);


namespace App\Component\Migration\Helper;


use App\Component\Migration\ImportAttributes;
use Ramsey\Uuid\Uuid;

/**
 * Class AttributeHelper
 * @package App\Component\Migration\Helper
 */
class AttributeHelper
{
    /**
     * @param $thesaurusId
     * @param string $name
     * @param string $modelType
     * @param \PDO $psql
     * @param \PDO $mysql
     */
    public static function importAttribute(string $thesaurusValue, string $categoryName, string $modelType, \PDO $psql, \PDO $mysql)
    {
        // first we get or create the attribute

        // we try to get the attribute
        $stm = $mysql->prepare('SELECT * FROM thesaurus WHERE thesaurus_id = :thesaurusId');
        $stm->execute(['thesaurusId' => $thesaurusValue]);

        // if there is a result, that means we extract the corresponding thesaurus row in mysql in $attribute
        if ($thesaurus = $stm->fetch()) {
            $attribute = self::getExistingAttribute($thesaurus, $psql, $mysql)['id'];
        }
        // if there is no attribute corresponding we need to create a new attribute
        else {
            $attribute = self::createAttribute($thesaurusValue, $categoryName, $modelType, $psql, $mysql);
        }

        // then we import the relationship

        // get id of the last related entity inserted
        $stm = $psql->prepare('SELECT currval(pg_get_serial_sequence(\''.$modelType.'\',\'id\')) as item_id');
        $stm->execute();
        $lastItemId = $stm->fetch()['item_id'];

        $params = [
            'item' => $lastItemId,
            'attribute' => $attribute,
        ];

        // insert attribute into psql entity_attribute table
        $stm = $psql->prepare('INSERT INTO '.$modelType.'_attribute ('.$modelType.'_id, attribute_id) VALUES (:item, :attribute)');
        $stm->execute($params);
    }

    /**
     * @param $thesaurus
     * @param $psql
     * @param $mysql
     * @return array|null
     */
    static function getExistingAttribute($thesaurus, $psql, $mysql): ?array
    {
        // find the corresponding postgres category
        if(!$categoryId = CategoryHelper::getExistingCategory($thesaurus['code_id'],  $psql, $mysql)) {
            throw new \Error('No category found for code '.$thesaurus['code_id']);
        }

        // we find the corresponding postgres attribute
        $stm = $psql->prepare('SELECT * FROM attribute WHERE title = :title AND category_id = :category');
        $stm->execute([
            'title' => $thesaurus['title'],
            'category' => $categoryId
        ]);

        //error here : can't find a result
        // adapted from a Broadway musical & 341
        return $stm->fetch();
    }

    /**
     * @Description : create a new attribute if it was not already a thesaurus
     * can create the category too
     *
     * @param string $thesaurusValue
     * @param string $category
     * @param string $model
     * @param \PDO $pgsql
     * @param \PDO $mysql
     */
    static public function createAttribute(
        string $thesaurusValue,
        string $category,
        string $model,
        \PDO $pgsql,
        \PDO $mysql
    ):int
    {
        // get the attribute category already exists
        $categoryId = CategoryHelper::createCategory($category, $model, $pgsql);
        // create a new category in psql, if there is an error, we will have to find the existing familly
        $uuid = Uuid::uuid4()->toString();
        $date = new \DateTime();
        $date = $date->format('Y-m-d H:i:s');

        // if attribute already exist we don't save it and just get the id
        $stm = $pgsql->prepare('SELECT id FROM attribute WHERE title = :title AND category_id = :categoryId ');
        $stm->execute([
            'title' => $thesaurusValue,
            'categoryId' => $categoryId,
        ]);

        if ($result = $stm->fetch()['id']) {
            return $result;
        }

        // else we create a new attribute
        $params = [
            'title' => $thesaurusValue,
            'definition' => null,
            'example' => null,
            'createdAt' => $date,
            'updatedAt' => $date,
            'uuid' => $uuid,
            'categoryId' => $categoryId,
            'mysqlId' => null, // it's a new attribute
        ];

        ImportAttributes::saveAttribute($params, $pgsql);

        //get last attribute id = the new attribute we have just created
        $stm = $pgsql->prepare('SELECT currval(pg_get_serial_sequence(\'attribute\',\'id\')) as attribute_id');
        $stm->execute();
        return  $stm->fetch()['attribute_id'];
    }
}
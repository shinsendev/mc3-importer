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
            $attribute = self::getExistingAttribute($thesaurus, $psql, $mysql);
        }
        // if there is no attribute corresponding we need to create a new attribute
        else {
            $attribute = self::createAttribute($thesaurusValue, $categoryName, $modelType, $psql, $mysql);
        }

        // then we import the relationship

        // get film id of the last related entity inserted
        $stm = $psql->prepare('  SELECT currval(pg_get_serial_sequence(\''.$modelType.'\',\'id\')) as item_id');
        $stm->execute();
        $lastItemId = $stm->fetch()['item_id'];

        $params = [
            'item' => $lastItemId,
            'attribute' => $attribute['id'],
        ];

        // insert attribute into psql entity_attribute table
        $stm = $psql->prepare('INSERT INTO '.$modelType.'_attribute (film_id, attribute_id) VALUES (:item, :attribute)');
        $stm->execute($params);
    }

    static function getExistingAttribute($thesaurus, $psql, $mysql): ?array
    {
        // find the corresponding postgres category
        $categoryId = CategoryHelper::getExistingCategory($thesaurus['code_id'],  $psql, $mysql);

        // it's here we find the corresponding postgres attribute
        $stm = $psql->prepare('SELECT * FROM attribute WHERE title = :title AND category_id = :category');
        $stm->execute([
            'title' => $thesaurus['title'],
            'category' => $categoryId
        ]);

        return $stm->fetch();
    }

    /**
     * @Description : create a new attribute if it was not already a thesaurus
     * can create the category too
     *
     * @param string $name
     * @param \PDO $psql
     * @param \PDO $mysql
     */
    static public function createAttribute(string $thesaurusValue, string $category, string $model, \PDO $pgsql, \PDO $mysql)
    {
        // get the attribute category already exists
        $categoryId = CategoryHelper::getCategory($category, $model, $pgsql, $mysql);
        dd($categoryId);

        // create a new category in psql, if there is an error, we will have to find the existing familly
        try {
            $uuid = Uuid::uuid4()->toString();
            $date = new \DateTime();
            $date = $date->format('Y-m-d H:i:s');

            $params = [
                'title' => $origin,
                'definition' => null,
                'example' => null,
                'createdAt' => $date,
                'updatedAt' => $date,
                'uuid' => $uuid,
                'categoryId' => $categoryId,
            ];

            ImportAttributes::saveAttribute($params, $psql);

        } catch (\Error $e) {
            //todo: add some logic and test
          dd($e);
        }
    }
}
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
    public static function importAttribute($thesaurusId, string $origin, string $modelType, \PDO $psql, \PDO $mysql)
    {
        // select corresponding mysql thesaurus : if thesaurusId is an integer it could be a foreign key
        if (gettype($thesaurusId) === 'integer') {
            $stm = $mysql->prepare('SELECT * FROM thesaurus WHERE thesaurus_id = :thesaurusId');
            $stm->execute(['thesaurusId' => $thesaurusId]);
            // if there is no attribute corresponding, it was not a foreign, we create a new attribute with the value
            if (!$thesaurus = $stm->fetch()) {
                AttributeHelper::createAttribute($thesaurusId, $origin, $psql, $mysql);
            }
        }
        // if thesaurusId is not a number so not an id we create a new attribute
        else {
            AttributeHelper::createAttribute($thesaurusId, $origin, $psql, $mysql);
        }

        // find corresponding postgres category
        $categoryId = CategoryHelper::getCategoryId($thesaurus,  $psql, $mysql);

        // find corresponding postgres attribute
        $stm = $psql->prepare('SELECT * FROM attribute WHERE title = :title AND category_id = :category');
        $stm->execute([
            'title' => $thesaurus['title'],
            'category' => $categoryId
        ]);
        $attribute = $stm->fetch();

        // get film id of the last related entity inserted
        $stm = $psql->prepare('  SELECT currval(pg_get_serial_sequence(\''.$modelType.'\',\'id\'));
');
//        $stm = $psql->prepare('SELECT LASTVAL() as item_id');
        $stm->execute();
        $lastItemId = $stm->fetch()['item_id'];

        // insert attribute into psql entity_attribute table
        $stm = $psql->prepare('INSERT INTO '.$modelType.'_attribute (film_id, attribute_id) VALUES (:item, :attribute)');
        $stm->execute([
            'item' => $lastItemId,
            'attribute' => $attribute['id'],
        ]);
    }

    /**
     * @Description : create a new attribute if it was not already a thesaurus
     * can create the category too
     *
     * @param $attribute
     * @param string $name
     * @param \PDO $psql
     * @param \PDO $mysql
     */
    static public function createAttribute($attribute, $categoryId, string $origin, \PDO $psql, \PDO $mysql)
    {
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
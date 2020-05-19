<?php

declare(strict_types=1);


namespace App\Component\Migration;


use App\Component\Migration\Helper\MigrationHelper;

class ImportThesaurusComments implements ImporterInterface
{
    static public function insert(\PDO $pgsql, array $thesaurus, \PDO $mysql, array $params = []): void
    {
        if ($thesaurus['comment']) {
            // then we save the comment
            $basics = MigrationHelper::createBaseParams();
            $sql = 'INSERT INTO comment (content, created_at, updated_at, uuid) VALUES(:content, :createdAt, :updatedAt, :uuid)';
            $stm = $pgsql->prepare($sql);
            $stm->execute([
                'content' => $thesaurus['comment'],
                'createdAt' => $basics['date'],
                'updatedAt' => $basics['date'],
                'uuid' => $basics['uuid'],
                // user
            ]);
        }
    }

}
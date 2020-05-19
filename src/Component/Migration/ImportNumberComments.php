<?php

declare(strict_types=1);


namespace App\Component\Migration;


use App\Component\Migration\Helper\MigrationHelper;

class ImportNumberComments implements ImporterInterface
{
    static public function insert(\PDO $pgsql, array $number, \PDO $mysql, array $params = []): void
    {
        $commentsNamesList = ['arranger_comment', 'comment_title', 'comment_tc', 'comment_structure', 'comment_shots', 'comment_performance', 'comment_backstage', 'comment_theme', 'comment_mood', 'comment_dance', 'comment_music', 'comment_director', 'comment_reference'];

        $value = '';

        // we add all the content in one variable
        foreach ($commentsNamesList as $commentType) {
            if ($number[$commentType]) {
                $value .= $commentType.' = '.$number[$commentType].' / ';
            }
        }

        // then we save the comment
        $basics = MigrationHelper::createBaseParams();
        $sql = 'INSERT INTO comment (content, created_at, updated_at, uuid) VALUES(:content, :createdAt, :updatedAt, :uuid)';
        $stm = $pgsql->prepare($sql);
        $stm->execute([
            'content' => $value,
            'createdAt' => $basics['date'],
            'updatedAt' => $basics['date'],
            'uuid' => $basics['uuid'],
            // user
        ]);
    }

}
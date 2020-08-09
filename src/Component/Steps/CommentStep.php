<?php

declare(strict_types=1);


namespace App\Component\Steps;


use App\Component\Migration\Helper\MigrationHelper;

class CommentStep implements StepInterface
{
    static function execute()
    {
        // numbers comments
        MigrationHelper::importAll('number','App\Component\Migration\ImportNumberComments::insert', 500);
        // thesaurus comments
        MigrationHelper::importAll('thesaurus','App\Component\Migration\ImportThesaurusComments::insert', 500);
    }


}
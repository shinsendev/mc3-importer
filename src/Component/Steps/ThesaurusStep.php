<?php

declare(strict_types=1);


namespace App\Component\Steps;


use App\Component\Migration\Helper\MigrationHelper;

class ThesaurusStep implements StepInterface
{
    static function execute()
    {
        MigrationHelper::importAll('code', 'App\Component\Migration\ImportCategories::insert', 500);
        MigrationHelper::importAll('thesaurus', 'App\Component\Migration\ImportAttributes::insert', 500);
    }
}
<?php

declare(strict_types=1);


namespace App\Component\Steps;


use App\Component\Migration\Helper\MigrationHelper;

class PersonStep implements StepInterface
{
    static function execute():void
    {
        MigrationHelper::importAll('person', 'App\Component\Migration\ImportPersons::insert', 500);
    }

}
<?php

declare(strict_types=1);


namespace App\Component\Steps;


use App\Component\Migration\Helper\MigrationHelper;

class ContributorStep implements StepInterface
{
    static function execute()
    {
        MigrationHelper::importAll('fos_user', 'App\Component\Migration\ImportUsers::insert', 500);
    }

}
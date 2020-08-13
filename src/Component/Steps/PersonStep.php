<?php

declare(strict_types=1);


namespace App\Component\Steps;


use App\Component\Migration\Helper\MigrationHelper;
use Psr\Log\LoggerInterface;

class PersonStep implements StepInterface
{
    static function execute(LoggerInterface $logger):void
    {
        MigrationHelper::importAll('person', 'App\Component\Migration\ImportPersons::insert', 500);
        $logger->info('People have been imported');

    }

}
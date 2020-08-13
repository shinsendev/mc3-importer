<?php

declare(strict_types=1);


namespace App\Component\Steps;


use App\Component\Migration\Helper\MigrationHelper;
use Psr\Log\LoggerInterface;

class ContributorStep implements StepInterface
{
    static function execute(LoggerInterface $logger)
    {
        MigrationHelper::importAll('fos_user', 'App\Component\Migration\ImportUsers::insert', 500);
        $logger->info('Contributors have been imported');
    }

}
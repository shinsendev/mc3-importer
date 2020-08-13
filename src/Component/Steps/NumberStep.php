<?php

declare(strict_types=1);


namespace App\Component\Steps;


use App\Component\Migration\Helper\MigrationHelper;
use App\Component\Number\NumberManyToManyAttributesImporter;
use Psr\Log\LoggerInterface;

class NumberStep implements StepInterface
{
    static function execute(LoggerInterface $logger):void
    {
        MigrationHelper::importAll('number', 'App\Component\Migration\ImportNumbers::insert', 500);
        $logger->info('Number and simple relations have been imported');
        // import number attributes
        NumberManyToManyAttributesImporter::import();
        $logger->info('Number many to many relations have been imported');
    }

}
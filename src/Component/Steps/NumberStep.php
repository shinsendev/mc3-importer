<?php

declare(strict_types=1);


namespace App\Component\Steps;


use App\Component\Migration\Helper\MigrationHelper;
use App\Component\Number\NumberManyToManyAttributesImporter;

class NumberStep implements StepInterface
{
    static function execute():void
    {
        MigrationHelper::importAll('number', 'App\Component\Migration\ImportNumbers::insert', 500);
        // import number attributes
        NumberManyToManyAttributesImporter::import();
    }

}
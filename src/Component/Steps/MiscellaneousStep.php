<?php

declare(strict_types=1);


namespace App\Component\Steps;


use App\Component\Migration\Helper\MigrationHelper;

class MiscellaneousStep implements StepInterface
{
    static function execute()
    {
        // import all distributors
        MigrationHelper::importAll('distributor', 'App\Component\Migration\ImportDistributors::insert', 500);
        // import all studios
        MigrationHelper::importAll('studio', 'App\Component\Migration\ImportStudios::insert', 500);
        // import films distributors links
        MigrationHelper::importRelations('film_has_distributor', 'film_distributor', 'film', 'distributor',1000);
        // import films studios links
        MigrationHelper::importRelations('film_has_studio', 'film_studio', 'film', 'studio',1000);    }

}
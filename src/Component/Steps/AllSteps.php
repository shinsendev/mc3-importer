<?php

namespace App\Component\Steps;

use App\Component\Migration\Helper\MigrationHelper;
use App\Component\Migration\ImportAttributes;
use App\Component\MySQL\Clean\MySQLClean;
use App\Component\Number\NumberManyToManyAttributesImporter;
use App\Component\PostgreSQL\Clean\PgSQLClean;
use Symfony\Component\HttpFoundation\JsonResponse;

class AllSteps implements StepInterface
{
    public static function execute():bool
    {
        set_time_limit(300);

        // Step 1: initialization
        InitializationStep::execute();

        // Step 2: import all contributors
        MigrationHelper::importAll('fos_user', 'App\Component\Migration\ImportUsers::insert', 500);

        // step 3: thesaurus
        MigrationHelper::importAll('code', 'App\Component\Migration\ImportCategories::insert', 500);
        MigrationHelper::importAll('thesaurus', 'App\Component\Migration\ImportAttributes::insert', 500);

        // step 4: people
        MigrationHelper::importAll('person', 'App\Component\Migration\ImportPersons::insert', 500);

        // step 5: films
        MigrationHelper::importAll('film', 'App\Component\Migration\ImportFilms::insert', 500);
        ImportAttributes::importExternalThesaurusString('censorship', 'censorship', 'film_has_censorship', 'film_attribute', 'film', 1000 );
        ImportAttributes::importExternalThesaurusString('state', 'state', 'film_has_state', 'film_attribute', 'film', 1000 ); // STEP 16

        // step 6: numbers
        MigrationHelper::importAll('number', 'App\Component\Migration\ImportNumbers::insert', 500);
        // import number attributes
        NumberManyToManyAttributesImporter::import();

        // step 7: songs
        MigrationHelper::importAll('song', 'App\Component\Migration\ImportSongs::insert', 500);
        // songs numbers relations
        MigrationHelper::importRelations('number_has_song', 'number_song', 'number', 'song',1000);
        // songs attributes relations
        ImportAttributes::importRelationsForExistingAttributes('song_has_songtype', 'song_attribute', 'songtype', 'song', 'attribute', 'song_id', 'songtype_id',  1000);


        // step 8: miscellaneous
        // import all distributors
        MigrationHelper::importAll('distributor', 'App\Component\Migration\ImportDistributors::insert', 500);
        // import all studios
        MigrationHelper::importAll('studio', 'App\Component\Migration\ImportStudios::insert', 500);
        // import films distributors links
        MigrationHelper::importRelations('film_has_distributor', 'film_distributor', 'film', 'distributor',1000);
        // import films studios links
        MigrationHelper::importRelations('film_has_studio', 'film_studio', 'film', 'studio',1000);

        // step 9:  comments
        // numbers comments
        MigrationHelper::importAll('number','App\Component\Migration\ImportNumberComments::insert', 500);
        // thesaurus comments
        MigrationHelper::importAll('thesaurus','App\Component\Migration\ImportThesaurusComments::insert', 500);


        // step 10: post process
        MySQLClean::finish();
        PgSQLClean::finish();

        return true;
    }
}
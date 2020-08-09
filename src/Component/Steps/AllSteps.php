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
    /**
     * @return bool
     */
    public static function execute():bool
    {
        set_time_limit(300);

        // Step 1: initialization
        InitializationStep::execute();

        // Step 2: import all contributors
        ContributorStep::execute();

        // step 3: thesaurus
        ThesaurusStep::execute();

        // step 4: people
        PersonStep::execute();

        // step 5: films
        FIlmStep::execute();

        // step 6: numbers
        NumberStep::execute();

        // step 7: songs
        SongStep::execute();

        // step 8: miscellaneous
        MiscellaneousStep::execute();

        // step 9:  comments
        CommentStep::execute();

        // step 10: post process
        PostProcessStep::execute();

        return true;
    }
}
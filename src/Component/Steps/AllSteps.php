<?php

namespace App\Component\Steps;

use Psr\Log\LoggerInterface;

class AllSteps implements StepInterface
{
    /**
     * @return bool
     */
    public static function execute(LoggerInterface $logger):bool
    {
        set_time_limit(300);
        $logger->info('Import has started');

        try {
        // Step 1: initialization
        InitializationStep::execute($logger);

        // Step 2: import all contributors
        ContributorStep::execute($logger);

        // step 3: thesaurus
        ThesaurusStep::execute($logger);

        // step 4: people
        PersonStep::execute($logger);

        // step 5: films
        FIlmStep::execute($logger);

        // step 6: numbers
        NumberStep::execute($logger);

        // step 7: songs
        SongStep::execute($logger);

        // step 8: miscellaneous
        MiscellaneousStep::execute($logger);

        // step 9:  comments
        CommentStep::execute($logger);

        // step 10: post process
        PostProcessStep::execute($logger);
        } catch (\Error $e) {
            throw new \Exception('Error during importer : '.$e->getMessage());
        }

        return true;
    }
}
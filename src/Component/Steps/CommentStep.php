<?php

declare(strict_types=1);


namespace App\Component\Steps;


use App\Component\Migration\Helper\MigrationHelper;
use Psr\Log\LoggerInterface;

class CommentStep implements StepInterface
{
    static function execute(LoggerInterface $logger)
    {
        // numbers comments
        MigrationHelper::importAll('number','App\Component\Migration\ImportNumberComments::insert', 500);
        // thesaurus comments
        MigrationHelper::importAll('thesaurus','App\Component\Migration\ImportThesaurusComments::insert', 500);
        $logger->info('Everything has been imported, post process step starts.');
    }

}
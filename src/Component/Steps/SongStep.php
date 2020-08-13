<?php

declare(strict_types=1);


namespace App\Component\Steps;


use App\Component\Migration\Helper\MigrationHelper;
use App\Component\Migration\ImportAttributes;
use Psr\Log\LoggerInterface;

class SongStep implements StepInterface
{
    static function execute(LoggerInterface $logger)
    {
        MigrationHelper::importAll('song', 'App\Component\Migration\ImportSongs::insert', 500);
        // songs numbers relations
        MigrationHelper::importRelations('number_has_song', 'number_song', 'number', 'song',1000);
        // songs attributes relations
        ImportAttributes::importRelationsForExistingAttributes('song_has_songtype', 'song_attribute', 'songtype', 'song', 'attribute', 'song_id', 'songtype_id',  1000);
        $logger->info('Songs have been imported');

    }

}
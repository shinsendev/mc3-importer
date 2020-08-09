<?php

declare(strict_types=1);


namespace App\Component\Steps;


use App\Component\Migration\Helper\MigrationHelper;
use App\Component\Migration\ImportAttributes;

class SongStep implements StepInterface
{
    static function execute()
    {
        MigrationHelper::importAll('song', 'App\Component\Migration\ImportSongs::insert', 500);
        // songs numbers relations
        MigrationHelper::importRelations('number_has_song', 'number_song', 'number', 'song',1000);
        // songs attributes relations
        ImportAttributes::importRelationsForExistingAttributes('song_has_songtype', 'song_attribute', 'songtype', 'song', 'attribute', 'song_id', 'songtype_id',  1000);
    }

}
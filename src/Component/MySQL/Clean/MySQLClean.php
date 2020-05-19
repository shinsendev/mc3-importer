<?php

declare(strict_types=1);


namespace App\Component\MySQL\Clean;


use App\Component\MySQL\Connection\MySQLConnection;

class MySQLClean
{
    static function clean()
    {
        $connection = MySQLConnection::connection();
        $sqlList = [
            'DROP TABLE IF EXISTS `stagenumber_has_costume`;',
            'DROP TABLE IF EXISTS `stagenumber_has_dancecontent`;',
            'DROP TABLE IF EXISTS `stagenumber_has_dancemble`;',
            'DROP TABLE IF EXISTS `stagenumber_has_dancesubgenre`;',
            'DROP TABLE IF EXISTS `stagenumber_has_dancingtype`;',
            'DROP TABLE IF EXISTS `stagenumber_has_editor`;',
            'DROP TABLE IF EXISTS `stagenumber_has_generalmood`;',
            'DROP TABLE IF EXISTS `stagenumber_has_genre`;',
            'DROP TABLE IF EXISTS `stagenumber_has_musensemble`;',
            'DROP TABLE IF EXISTS `stagenumber_has_musicalthesaurus`;',
            'DROP TABLE IF EXISTS `stagenumber_has_performer`;',
            'DROP TABLE IF EXISTS `number_has_stagenumber`;',
            'DROP TABLE IF EXISTS `song_has_stagenumber`;',
            'DROP TABLE IF EXISTS `stagenumber`;',
            'DROP TABLE IF EXISTS `song_has_tv`;',
            'DROP TABLE IF EXISTS `TV`;',
            'DROP TABLE IF EXISTS `song_has_disc`;',
            'DROP TABLE IF EXISTS `disc`;',
            'DROP TABLE IF EXISTS `song_has_radio`;',
            'DROP TABLE IF EXISTS `radio`;',
            'DROP TABLE IF EXISTS `film_has_stageshow`;',
            'DROP TABLE IF EXISTS `stageshow_has_book`;',
            'DROP TABLE IF EXISTS `stageshow_has_choreographer`;',
            'DROP TABLE IF EXISTS `stageshow_has_composer`;',
            'DROP TABLE IF EXISTS `stageshow_has_design`;',
            'DROP TABLE IF EXISTS `stageshow_has_director`;',
            'DROP TABLE IF EXISTS `stageshow_has_editor`;',
            'DROP TABLE IF EXISTS `stageshow_has_film`;',
            'DROP TABLE IF EXISTS `stageshow_has_lyricist`;',
            'DROP TABLE IF EXISTS `stageshow`;',
            'DROP TABLE IF EXISTS `number_has_validation_backstage`;',
            'DROP TABLE IF EXISTS `number_has_validation_cost`;',
            'DROP TABLE IF EXISTS `number_has_validation_dance`;',
            'DROP TABLE IF EXISTS `number_has_validation_director`;',
            'DROP TABLE IF EXISTS `number_has_validation_mood`;',
            'DROP TABLE IF EXISTS `number_has_validation_music`;',
            'DROP TABLE IF EXISTS `number_has_validation_performance`;',
            'DROP TABLE IF EXISTS `number_has_validation_reference`;',
            'DROP TABLE IF EXISTS `number_has_validation_shots`;',
            'DROP TABLE IF EXISTS `number_has_validation_structure`;',
            'DROP TABLE IF EXISTS `number_has_validation_tc`;',
            'DROP TABLE IF EXISTS `number_has_validation_theme`;',
            'DROP TABLE IF EXISTS `number_has_validation_title`;',
            'DROP TABLE IF EXISTS `validation`;',
            'DROP TABLE IF EXISTS `number_has_generalmood`;',
            'DROP TABLE IF EXISTS `migration_versions`;',
            'DROP TABLE IF EXISTS `film_has_director`;',

            'ALTER TABLE film DROP color;',
            'ALTER TABLE film DROP ratio;',
            'ALTER TABLE film DROP rights;',
            'ALTER TABLE film DROP negative;',
            'ALTER TABLE film DROP pna;',
            'ALTER TABLE film DROP us_rentals;',
            'ALTER TABLE film DROP foreign_rentals;',
            'ALTER TABLE film DROP total_rentals;',
            'ALTER TABLE film DROP us_boxoffice;',
            'ALTER TABLE film DROP foreign_boxoffice;',
            'ALTER TABLE film DROP total_boxoffice;',
            'ALTER TABLE film DROP source_eco;',

            'ALTER TABLE number DROP cost;',
            'ALTER TABLE number DROP cost_comment;',

            'ALTER TABLE song_has_composer CHANGE composer_id person_id INT(11);',
        ];

        foreach ($sqlList as $sql) {
            try {
                $connection->query($sql);
            } catch (\PDOException $e) {
                throw new \Error($sql. $e);
            }
        }
        
    }
}
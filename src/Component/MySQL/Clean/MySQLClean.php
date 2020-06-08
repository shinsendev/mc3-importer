<?php

declare(strict_types=1);


namespace App\Component\MySQL\Clean;


use App\Component\MySQL\Connection\MySQLConnection;
use App\Component\PostgreSQL\Connection\PostgreSQLConnection;

class MySQLClean
{
    static function clean()
    {
        $pgsqlConnection = PostgreSQLConnection::connection();

        $sqlList = [
            'DELETE FROM "user"',
            'DELETE FROM attribute',
            'DELETE FROM category',
            'DELETE FROM work',
            'DELETE FROM person',
            'DELETE FROM number',
            'DELETE FROM film',
            'DELETE FROM distributor',
            'DELETE FROM studio',
            'DELETE FROM song',
            'DELETE FROM comment',
        ];

        foreach ($sqlList as $sql) {
            try {
                $pgsqlConnection->query($sql);
            } catch (\PDOException $e) {
                throw new \Error($sql. $e);
            }
        }

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

    public static function finish()
    {
        $connection = PostgreSQLConnection::connection();
        // todo remove all mysql id

        // update all categories models
        $sqlList = [
            "UPDATE category SET model = 'song' WHERE code = 'songtype'",
            "UPDATE category SET model = 'number' WHERE code = 'begin_thesaurus'",
            "UPDATE category SET model = 'number' WHERE code = 'completeness_thesaurus'",
            "UPDATE category SET model = 'number' WHERE code = 'complet_options'",
            "UPDATE category SET model = 'number' WHERE code = 'costumes'; -- to delete",
            "UPDATE category SET model = 'number' WHERE code = 'dancemble';",
            "UPDATE category SET model = 'number' WHERE code = 'diegetic_thesaurus'",
            "UPDATE category SET model = 'number' WHERE code = 'diegetic_place_thesaurus'",
            "UPDATE category SET model = 'number' WHERE code = 'ending_thesaurus'",
            "UPDATE category SET model = 'number' WHERE code = 'stereotype'",
            "UPDATE category SET model = 'number' WHERE code = 'exoticism_thesaurus'",
            "UPDATE category SET model = 'number' WHERE code = 'general_localisation'; -- to delete",
            "UPDATE category SET model = 'number' WHERE code = 'imaginary'",
            "UPDATE category SET model = 'number' WHERE code = 'musensemble'",
            "UPDATE category SET model = 'number' WHERE code = 'musical_thesaurus'",
            "UPDATE category SET model = 'number' WHERE code = 'musician_thesaurus'",
            "UPDATE category SET model = 'number' WHERE code = 'performance_thesaurus'",
            "UPDATE category SET model = 'number' WHERE code = 'quotation_thesaurus'",
            "UPDATE category SET model = 'number' WHERE code = 'source_thesaurus'",
            "UPDATE category SET model = 'number' WHERE code = 'spectators_thesaurus'",
            "UPDATE category SET model = 'number' WHERE code = 'structure'",
            "UPDATE category SET model = 'number' WHERE code = 'tempo_thesaurus'",
            "UPDATE category SET model = 'number' WHERE code = 'general_mood'; -- to delete",
            "UPDATE category SET model = 'number' WHERE code = 'genre';",
            "UPDATE category SET model = 'number' WHERE code = 'dancing_type';",
            "UPDATE category SET model = 'number' WHERE code = 'dance_subgenre';",
            "UPDATE category SET model = 'number' WHERE code = 'dance_content';",
            "UPDATE category SET model = 'number' WHERE code = 'cast';",
            "UPDATE category SET model = 'film' WHERE code = 'adaptation';",
            "UPDATE category SET model = 'film' WHERE code = 'legion';",
            "UPDATE category SET model = 'film' WHERE code = 'verdict';",
            "UPDATE category SET model = 'film' WHERE code = 'protestant';",
            "UPDATE category SET model = 'film' WHERE code = 'harrison';",
            "UPDATE category SET model = 'film' WHERE code = 'board';",
            "UPDATE category SET model = 'film' WHERE code = 'censorship';",
            "UPDATE category SET model = 'film' WHERE code = 'state';",
            "DELETE FROM attribute WHERE category_id IN (SELECT id FROM category WHERE code = 'costumes' OR code = 'general_mood' OR code = 'general_localisation');",
            "DELETE FROM category WHERE code = 'costumes';",
            "DELETE FROM category WHERE code = 'general_mood';",
            "DELETE FROM category WHERE code = 'general_localisation';",
        ];

        foreach ($sqlList as $sql) {
            try {
                $connection->query($sql);
            } catch (\PDOException $e) {
                throw new \Error($sql . $e);
            }
        }
    }
}
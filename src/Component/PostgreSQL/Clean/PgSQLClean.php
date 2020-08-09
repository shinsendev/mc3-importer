<?php


namespace App\Component\PostgreSQL\Clean;


use App\Component\PostgreSQL\Connection\PostgreSQLConnection;

class PgSQLClean
{
    public static function clean()
    {
        $pgsqlConnection = PostgreSQLConnection::connection();

        $sqlList = [
            'DELETE FROM "user"',
            'DELETE FROM contributor',
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
            'ALTER TABLE contributor ADD COLUMN IF NOT EXISTS mysql_id INT;',
            'ALTER TABLE attribute ADD COLUMN IF NOT EXISTS mysql_id INT;',
            'ALTER TABLE category ADD COLUMN IF NOT EXISTS mysql_id INT;',
            'ALTER TABLE person ADD COLUMN IF NOT EXISTS mysql_id INT;',
            'ALTER TABLE number ADD COLUMN IF NOT EXISTS mysql_id INT;',
            'ALTER TABLE film ADD COLUMN IF NOT EXISTS mysql_id INT;',
            'ALTER TABLE distributor ADD COLUMN IF NOT EXISTS mysql_id INT;',
            'ALTER TABLE studio ADD COLUMN IF NOT EXISTS mysql_id INT;',
            'ALTER TABLE song ADD COLUMN IF NOT EXISTS mysql_id INT;',
        ];

        foreach ($sqlList as $sql) {
            try {
                $pgsqlConnection->query($sql);
            } catch (\PDOException $e) {
                throw new \Error($sql. $e);
            }
        }
    }

    public static function finish()
    {
        $pgsqlConnection = PostgreSQLConnection::connection();

        $sqlList = [
            'ALTER TABLE contributor DROP mysql_id;',
            'ALTER TABLE attribute DROP mysql_id;',
            'ALTER TABLE category DROP mysql_id;',
            'ALTER TABLE person DROP mysql_id;',
            'ALTER TABLE number DROP mysql_id;',
            'ALTER TABLE film DROP mysql_id;',
            'ALTER TABLE distributor DROP mysql_id;',
            'ALTER TABLE studio DROP mysql_id;',
            'ALTER TABLE song DROP mysql_id;',
            // change profession
            "UPDATE work SET profession='minor role' WHERE profession='figurant'",
        ];

        foreach ($sqlList as $sql) {
            try {
                $pgsqlConnection->query($sql);
            } catch (\PDOException $e) {
                throw new \Error($sql. $e);
            }
        }
        
    }
}
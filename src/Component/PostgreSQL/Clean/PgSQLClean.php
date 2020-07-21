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
    }

    public static function finish()
    {
        $pgsqlConnection = PostgreSQLConnection::connection();

        $sqlList = [
            'ALTER TABLE "user" DROP mysql_id;',
            'ALTER TABLE attribute DROP mysql_id;',
            'ALTER TABLE category DROP mysql_id;',
            'ALTER TABLE person DROP mysql_id;',
            'ALTER TABLE category DROP mysql_id;',
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
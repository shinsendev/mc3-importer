<?php


namespace App\Component\ImportEntity;


use App\Component\PostgreSQL\Connection\PostgreSQLConnection;

class ImportEntityManager
{
    const STARTED_STATUS = "started";
    const FAILED_STATUS = 'failed';
    const SUCCESS_STATUS = 'success';

    public static function updateImportEntity(string $status)
    {
        $connection = PostgreSQLConnection::connection();
        $rsl = $connection->prepare('UPDATE import SET status = :status WHERE id IN (SELECT max(id) FROM import)');
        $rsl->execute(['status' => $status]);
    }
}
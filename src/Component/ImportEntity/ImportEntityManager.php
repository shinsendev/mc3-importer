<?php


namespace App\Component\ImportEntity;


use App\Component\PostgreSQL\Connection\PostgreSQLConnection;

class ImportEntityManager
{
    const STARTED_STATUS = "started";
    const FAILED_STATUS = 'failed';
    const SUCCESS_STATUS = 'success';

    public static function updateImportEntity(string $status, $inProgress = false)
    {
        $connection = PostgreSQLConnection::connection();
        $rsl = $connection->prepare('UPDATE import SET status = :status, updated_at = NOW(), in_progress = :progress WHERE id IN (SELECT max(id) FROM import)');
        $rsl->execute(['status' => $status, 'progress' => $inProgress]);
    }
}
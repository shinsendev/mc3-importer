<?php

declare(strict_types=1);

namespace App\Component\Migration;

/**
 * Interface ImporterInterface
 * @package App\Component\Migration
 */
interface ImporterInterface
{
    static public function insert(\PDO $pgsql, array $itemsList, \PDO $mysql);
}
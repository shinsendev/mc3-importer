<?php


namespace App\Component\Operation;


use App\Component\ImportEntity\ImportEntityManager;
use App\Component\MySQL\Initialization\MySQLInitialization;

class InitOperation
{
    public static function init():void
    {
        MySQLInitialization::init();
        ImportEntityManager::updateImportEntity(ImportEntityManager::STARTED_STATUS);
    }
}
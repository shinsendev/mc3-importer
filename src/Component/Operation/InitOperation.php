<?php


namespace App\Component\Operation;


use App\Component\MySQL\Initialization\MySQLInitialization;

class InitOperation
{
    public static function init():void
    {
        MySQLInitialization::init();
    }
}
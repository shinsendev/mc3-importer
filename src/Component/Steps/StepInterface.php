<?php


namespace App\Component\Steps;


use Psr\Log\LoggerInterface;

interface StepInterface
{
    static function execute(LoggerInterface $logger);
}
<?php

namespace App\Component\Steps;

use App\Component\Migration\Helper\MigrationHelper;
use Symfony\Component\HttpFoundation\JsonResponse;

class AllSteps
{
    public static function importAll():bool
    {
        set_time_limit(300);

        // Step 1: initialization
        InitializationStep::execute();

        // Step 2: import all contributors
        MigrationHelper::importAll('fos_user', 'App\Component\Migration\ImportUsers::insert', 500);

        // step 3: import all ...


//        $this->addFlash('success', 'Users imported!');

        return true;
    }
}
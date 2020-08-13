<?php

namespace App\Controller;

use App\Component\Security\Security;
use App\Component\Steps\AllSteps;
use App\Component\Steps\CommentStep;
use App\Component\Steps\ContributorStep;
use App\Component\Steps\FIlmStep;
use App\Component\Steps\InitializationStep;
use App\Component\Steps\MiscellaneousStep;
use App\Component\Steps\NumberStep;
use App\Component\Steps\PersonStep;
use App\Component\Steps\PostProcessStep;
use App\Component\Steps\SongStep;
use App\Component\Steps\ThesaurusStep;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Process;

/**
 * Class ImportController
 * @package App\Controller
 */
class ImportController extends AbstractController
{
    const NO_AURTHORIZATION_MESSAGE = 'You are not authorized';

    /**
     * @Route("/import/all", name="import", methods={"POST"})
     *
     */
    public function import(Request $request, LoggerInterface $logger)
    {
        // check if client is granted to make import
        if (!Security::isGranted($request)) {
            return new JsonResponse(self::NO_AURTHORIZATION_MESSAGE, 403);
        }

        $logger->info('Import process is about to be launched');
        $process = Process::fromShellCommandline('cd ../ && sh start.sh');
        $process->start();
        sleep($_ENV['SLEEP_TIME']); // not sure exactly why, but processus is killed in prod if we don't add it
        return new JsonResponse('Import has been launched.', 200);
    }

}

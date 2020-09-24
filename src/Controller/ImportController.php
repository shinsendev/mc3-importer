<?php

namespace App\Controller;

use App\Component\Event\ImportListener;
use App\Component\Security\Security;
use App\Message\ImportLauncher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
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
    public function import(Request $request, LoggerInterface $logger, MessageBusInterface $messageBus)
    {
        // check if client is granted to make import
        if (!Security::isGranted($request)) {
            return new JsonResponse(self::NO_AURTHORIZATION_MESSAGE, 403);
        }

        $logger->info('Import process is about to be launched');
        $message = new ImportLauncher();
        $messageBus->dispatch($message);

        return new JsonResponse('Import has been launched.', 200);
    }

}

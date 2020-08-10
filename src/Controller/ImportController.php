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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
    public function import(Request $request)
    {
        // check if client is granted to make import
        if (!Security::isGranted($request)) {
            return new JsonResponse(self::NO_AURTHORIZATION_MESSAGE, 403);
        }

        AllSteps::execute();
        return new JsonResponse('Import succeeds.', 200);
    }

    /**
     * @Route("/import/init", name="import_init",  methods={"POST"})
     */
    public function init(Request $request)
    {
        // check if client is granted to make import
        if (!Security::isGranted($request)) {
            return new JsonResponse(self::NO_AURTHORIZATION_MESSAGE, 403);
        }

        InitializationStep::execute();
        return new JsonResponse('Initialisation ok, DB structure has been created and data has been imported &DBs has been cleaned.', 200);
    }

    /**
     * @Route("/import/contributors", name="import_contributors", methods={"POST"})
     */
    public function importContributors(Request $request)
    {
        // check if client is granted to make import
        if (!Security::isGranted($request)) {
            return new JsonResponse(self::NO_AURTHORIZATION_MESSAGE, 403);
        }

        ContributorStep::execute();
        return new JsonResponse('Contributors have been successfully imported.', 200);
    }


    /**
     * @Route("/import/categories", name="import_thesaurus", methods={"POST"})
     */
    public function importCategories(Request $request)
    {
        // check if client is granted to make import
        if (!Security::isGranted($request)) {
            return new JsonResponse(self::NO_AURTHORIZATION_MESSAGE, 403);
        }

        ThesaurusStep::execute();
        return new JsonResponse('Categories and attributes have been successfully imported.', 200);
    }


    /**
     * @Route("/import/people", name="import_people",  methods={"POST"})
     */
    public function importPeople(Request $request)
    {
        // check if client is granted to make import
        if (!Security::isGranted($request)) {
            return new JsonResponse(self::NO_AURTHORIZATION_MESSAGE, 403);
        }

        PersonStep::execute();
        return new JsonResponse('People have been successfully imported.', 200);
    }


    /**
     * @Route("/import/films", name="import_films")
     */
    public function importFilms(Request $request)
    {
        // check if client is granted to make import
        if (!Security::isGranted($request)) {
            return new JsonResponse(self::NO_AURTHORIZATION_MESSAGE, 403);
        }

        FIlmStep::execute();
        return new JsonResponse('Films have been successfully imported.', 200);
    }

    /**
     * @Route("/import/numbers", name="import_numbers")
     */
    public function importNumbers(Request $request)
    {
        // check if client is granted to make import
        if (!Security::isGranted($request)) {
            return new JsonResponse(self::NO_AURTHORIZATION_MESSAGE, 403);
        }

        NumberStep::execute();
        return new JsonResponse('Numbers have been successfully imported.', 200);
    }

    /**
     * @Route("/import/songs", name="import_songs")
     */
    public function importSongs(Request $request)
    {
        // check if client is granted to make import
        if (!Security::isGranted($request)) {
            return new JsonResponse(self::NO_AURTHORIZATION_MESSAGE, 403);
        }

        SongStep::execute();
        return new JsonResponse('Songs have been successfully imported.', 200);
    }

    /**
     * @Route("/import/miscellaneous", name="import_miscellaneous")
     */
    public function importMiscellaneous(Request $request)
    {
        MiscellaneousStep::execute();
        return new JsonResponse('Studios and distributors have been successfully imported.', 200);

    }

    /**
     * @Route("/import/comments", name="import_comments")
     */
    public function importComments(Request $request)
    {
        // check if client is granted to make import
        if (!Security::isGranted($request)) {
            return new JsonResponse(self::NO_AURTHORIZATION_MESSAGE, 403);
        }

        CommentStep::execute();
        return new JsonResponse('Comments have been successfully imported.', 200);
    }

    /**
     * @Route("/import/postprocess", name="post_process")
     */
    public function postProcess(Request $request)
    {
        // check if client is granted to make import
        if (!Security::isGranted($request)) {
            return new JsonResponse(self::NO_AURTHORIZATION_MESSAGE, 403);
        }

        PostProcessStep::execute();
        return new JsonResponse('Post process operations have been successfully been executed.', 200);
    }

}

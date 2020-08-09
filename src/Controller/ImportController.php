<?php

namespace App\Controller;

use App\Component\Migration\Helper\MigrationHelper;
use App\Component\Migration\ImportAttributes;
use App\Component\MySQL\Clean\MySQLClean;
use App\Component\MySQL\Import\MySQLImport;
use App\Component\Number\NumberManyToManyAttributesImporter;
use App\Component\Operation\CleanOperation;
use App\Component\Operation\InitOperation;
use App\Component\PostgreSQL\Clean\PgSQLClean;
use App\Component\Security\Security;
use App\Component\Steps\AllSteps;
use App\Component\Steps\InitializationStep;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ImportController
 * @package App\Controller
 */
class ImportController extends AbstractController
{
    /**
     * @Route("/import/all", name="import", methods={"POST"})
     *
     */
    public function import(Request $request)
    {
        // check if client is granted to make import
        if (!Security::isGranted($request)) {
            return new JsonResponse('Not authorized to execute the import.', 403);
        }

        // launch the import
        AllSteps::execute();

        return new JsonResponse('Import succeeds.', 200);
    }

    /**
     * STEP 1
     *
     * @Route("/import/init", name="import_init")
     */
    public function init()
    {
        InitializationStep::execute();
        $this->addFlash('success', 'Initialisation ok, DB structure has been created and data has been imported &DBs has been cleaned.');
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/categories", name="import_thesaurus")
     */
    public function importCategories()
    {
        MigrationHelper::importAll('code', 'App\Component\Migration\ImportCategories::insert', 500);
        MigrationHelper::importAll('thesaurus', 'App\Component\Migration\ImportAttributes::insert', 500);


        return $this->redirectToRoute('home');
    }


    /**
     * @Route("/import/films", name="import_films")
     */
    public function importFilms()
    {
        MigrationHelper::importAll('film', 'App\Component\Migration\ImportFilms::insert', 500);

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/numbers", name="import_numbers")
     */
    public function importNumbers()
    {
        MigrationHelper::importAll('number', 'App\Component\Migration\ImportNumbers::insert', 500);

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/songs", name="import_songs")
     */
    public function importSongs()
    {
        MigrationHelper::importAll('song', 'App\Component\Migration\ImportSongs::insert', 500);

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/persons", name="import_persons")
     */
    public function importPersons()
    {
        MigrationHelper::importAll('person', 'App\Component\Migration\ImportPersons::insert', 500);

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/studios", name="import_studios")
     */
    public function importStudios()
    {
        MigrationHelper::importAll('studio', 'App\Component\Migration\ImportStudios::insert', 500);

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/distributors", name="import_distributors")
     */
    public function importDistributors()
    {
        MigrationHelper::importAll('distributor', 'App\Component\Migration\ImportDistributors::insert', 500);

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/users", name="import_users")
     */
    public function importUsers()
    {
        MigrationHelper::importAll('fos_user', 'App\Component\Migration\ImportUsers::insert', 500);

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/film-studio-relations", name="import_film_studio_relations")
     */
    public function importFilmsStudioRelations()
    {
        MigrationHelper::importRelations('film_has_studio', 'film_studio', 'film', 'studio',1000);

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/film-distributor-relations", name="import_film_distributor_relations")
     */
    public function importFilmsDistributorRelations()
    {
        MigrationHelper::importRelations('film_has_distributor', 'film_distributor', 'film', 'distributor', 1000);

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/film-censorship-thesaurus", name="import_film_censorship_thesaurus")
     */
    public function importFilmCensorshipThesaurus()
    {
        ImportAttributes::importExternalThesaurusString('censorship', 'censorship', 'film_has_censorship', 'film_attribute', 'film', 1000, true );

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/film-state-thesaurus", name="import_film_state_thesaurus")
     */
    public function importFilmStateThesaurus()
    {
        ImportAttributes::importExternalThesaurusString('state', 'state', 'film_has_state', 'film_attribute', 'film', 1000, true );

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/number-attributes", name="import_number_attributes")
     */
    public function importNumberAttributes()
    {
       NumberManyToManyAttributesImporter::import();
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/comments", name="import_comments")
     */
    public function importComments()
    {
        // numbers
        MigrationHelper::importAll('number','App\Component\Migration\ImportNumberComments::insert', 500);

        // thesaurus
        MigrationHelper::importAll('thesaurus','App\Component\Migration\ImportThesaurusComments::insert', 500);

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/song-number", name="import_song_number_relations")
     */
    public function importSongNumberRelations()
    {
        MigrationHelper::importRelations('number_has_song', 'number_song', 'number', 'song',1000);

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/song-attributes", name="import_song_attributes")
     */
    public function importSongAttributes()
    {
        ImportAttributes::importRelationsForExistingAttributes('song_has_songtype', 'song_attribute', 'songtype', 'song', 'attribute', 'song_id', 'songtype_id',  1000);

        return $this->redirectToRoute('home');
    }
}

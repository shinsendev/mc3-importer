<?php

namespace App\Controller;

use App\Component\Migration\Helper\MigrationHelper;
use App\Component\Migration\ImportAttributes;
use App\Component\MySQL\Clean\MySQLClean;
use App\Component\MySQL\Import\MySQLImport;
use App\Component\MySQL\Initialization\MySQLInitialization;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ImportController
 * @package App\Controller
 */
class ImportController extends AbstractController
{
    /**
     * @Route("/import/all", name="import_all")
     */
    public function all()
    {
        // prepare import by building mc2 MySQL database
        MySQLInitialization::init();
        MySQLImport::import('../data/mc2.sql');
        MySQLClean::clean();

        // import all users
        MigrationHelper::importAll('fos_user', 'App\Component\Migration\ImportUsers::insert', 500);

        // import categories (from code)
        MigrationHelper::importAll('code', 'App\Component\Migration\ImportCategories::insert', 500);

        // import attributes with categories (from thesaurus)
        MigrationHelper::importAll('thesaurus', 'App\Component\Migration\ImportAttributes::insert', 500);
        // import all persons
        MigrationHelper::importAll('person', 'App\Component\Migration\ImportPersons::insert', 500);

        // import films
        MigrationHelper::importAll('film', 'App\Component\Migration\ImportFilms::insert', 500);

        // import numbers
        MigrationHelper::importAll('number', 'App\Component\Migration\ImportNumbers::insert', 500);

        // import songs
        MigrationHelper::importAll('song', 'App\Component\Migration\ImportSongs::insert', 500);

        // import all distributors
        MigrationHelper::importAll('distributor', 'App\Component\Migration\ImportDistributors::insert', 500);

        // import all studios
        MigrationHelper::importAll('studio', 'App\Component\Migration\ImportStudios::insert', 500);

        // import films distributors links
        MigrationHelper::importRelations('film_has_distributor', 'film_distributor', 'film', 'distributor',1000);

        // import films studios links
        MigrationHelper::importRelations('film_has_studio', 'film_studio', 'film', 'studio',1000);

        // import censorship
//        ImportAttributes::importExternalThesaurusString('censorship', 'censorship', 'film_has_censorship', 'film', 1000 );

        // import states

        $this->addFlash('success', 'Everything is ok');
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/init", name="import_init")
     */
    public function init()
    {
        MySQLInitialization::init();

        $this->addFlash('success', 'Initialisation ok');
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/creation", name="import_creation")
     */
    public function create()
    {
        MySQLImport::import('../data/mc2.sql');

        $this->addFlash('success', 'DB structure has been created and data has been imported');
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/clean", name="import_clean")
     */
    public function clean()
    {
        MySQLClean::clean();
        $this->addFlash('success', 'DB has been cleaned.');
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/categories", name="import_categories")
     */
    public function importCategories()
    {
        MigrationHelper::importAll('code', 'App\Component\Migration\ImportCategories::insert', 500);

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/attributes", name="import_attributes")
     */
    public function importAttributes()
    {
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
        // import complet_options
        ImportAttributes::importRelationsForExistingAttributes('number_has_completoptions', 'number_attribute', 'complet_options', 'number', 'attribute', 'number_id', 'completoptions_id',  1000);

        // import dancecontent
        ImportAttributes::importRelationsForExistingAttributes('number_has_dancecontent', 'number_attribute', 'dance_content', 'number', 'attribute', 'number_id', 'dancecontent_id',  1000);

        // import dancemble
        ImportAttributes::importRelationsForExistingAttributes('number_has_dancemble', 'number_attribute', 'dancemble', 'number', 'attribute', 'number_id', 'dancemble_id',  1000);

        // import dancesubgenre
        ImportAttributes::importRelationsForExistingAttributes('number_has_dancesubgenre', 'number_attribute', 'dance_subgenre', 'number', 'attribute', 'number_id', 'dancesubgenre_id',  1000);

        // dancingtype

        // diegeticplace

        // exoticismthesaurus

        // genre => topic

        // imaginary

        // musensemble

        // musicalthesaurus

        // quotationthesaurus

        // source

        // stereotype

        return $this->redirectToRoute('home');
    }
}

<?php

namespace App\Controller;

use App\Component\Migration\Helper\MigrationHelper;
use App\Component\Migration\ImportAttributes;
use App\Component\MySQL\Clean\MySQLClean;
use App\Component\MySQL\Import\MySQLImport;
use App\Component\Operation\CleanOperation;
use App\Component\Operation\InitOperation;
use App\Component\PostgreSQL\Clean\PgSQLClean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ImportController
 * @package App\Controller
 */
class ImportController extends AbstractController
{
    /**
     * @Route("/import/all", name="import_all")
     *
     */
    public function all()
    {
        set_time_limit(300);

        InitOperation::init(); // STEP 1
        // prepare import by building mc2 MySQL database
        MySQLImport::import('../data/mc2.sql'); // STEP 2
        CleanOperation::clean(); // STEP 3

        // import all users
        MigrationHelper::importAll('fos_user', 'App\Component\Migration\ImportUsers::insert', 500); // STEP 4
        $this->addFlash('success', 'Users imported!');

        // import categories (from code)
        MigrationHelper::importAll('code', 'App\Component\Migration\ImportCategories::insert', 500); // STEP 5
        $this->addFlash('success', 'Categories imported!');

        // import attributes with categories (from thesaurus)
        MigrationHelper::importAll('thesaurus', 'App\Component\Migration\ImportAttributes::insert', 500); // STEP 6
        $this->addFlash('success', 'Attributes imported!');

        // import all persons
        MigrationHelper::importAll('person', 'App\Component\Migration\ImportPersons::insert', 500); // STEP 7
        $this->addFlash('success', 'Persons imported!');

        // import films
        MigrationHelper::importAll('film', 'App\Component\Migration\ImportFilms::insert', 500); // STEP 8
        $this->addFlash('success', 'Films imported!');

        // import numbers
        MigrationHelper::importAll('number', 'App\Component\Migration\ImportNumbers::insert', 500); // STEP 9
        $this->addFlash('success', 'Numbers imported!');

        // import songs
        MigrationHelper::importAll('song', 'App\Component\Migration\ImportSongs::insert', 500); // STEP 10
        $this->addFlash('success', 'Songs imported!');

        // songs numbers relations
        MigrationHelper::importRelations('number_has_song', 'number_song', 'number', 'song',1000);

        // import all distributors
        MigrationHelper::importAll('distributor', 'App\Component\Migration\ImportDistributors::insert', 500); // STEP 11

        // import all studios
        MigrationHelper::importAll('studio', 'App\Component\Migration\ImportStudios::insert', 500); // STEP 12

        // import films distributors links
        MigrationHelper::importRelations('film_has_distributor', 'film_distributor', 'film', 'distributor',1000); // STEP 13

        // import films studios links
        MigrationHelper::importRelations('film_has_studio', 'film_studio', 'film', 'studio',1000); // STEP 14

        // import censorship
        ImportAttributes::importExternalThesaurusString('censorship', 'censorship', 'film_has_censorship', 'film_attribute', 'film', 1000, true ); // STEP 15

        // import states
        ImportAttributes::importExternalThesaurusString('state', 'state', 'film_has_state', 'film_attribute', 'film', 1000, true ); // STEP 16

        // import number attributes

        // import complet_options
        ImportAttributes::importRelationsForExistingAttributes('number_has_completoptions', 'number_attribute', 'complet_options', 'number', 'attribute', 'number_id', 'completoptions_id',  1000);

        // import dancecontent
        ImportAttributes::importRelationsForExistingAttributes('number_has_dancecontent', 'number_attribute', 'dance_content', 'number', 'attribute', 'number_id', 'dancecontent_id',  1000);

        // import dancemble
        ImportAttributes::importRelationsForExistingAttributes('number_has_dancemble', 'number_attribute', 'dancemble', 'number', 'attribute', 'number_id', 'dancemble_id',  1000);

        // import dancesubgenre
        ImportAttributes::importRelationsForExistingAttributes('number_has_dancesubgenre', 'number_attribute', 'dance_subgenre', 'number', 'attribute', 'number_id', 'dancesubgenre_id',  1000);

        // dancingtype
        ImportAttributes::importRelationsForExistingAttributes('number_has_dancingtype', 'number_attribute', 'dancing_type', 'number', 'attribute', 'number_id', 'dancingtype_id',  1000);

        // diegeticplace
        ImportAttributes::importRelationsForExistingAttributes('number_has_diegeticplace', 'number_attribute', 'diegetic_place_thesaurus', 'number', 'attribute', 'number_id', 'diegetic_place_thesaurus_id',  1000);

        // exoticismthesaurus
        ImportAttributes::importRelationsForExistingAttributes('number_has_exoticismthesaurus', 'number_attribute', 'exoticism_thesaurus', 'number', 'attribute', 'number_id', 'exoticism_thesaurus_id',  1000);

        // genre => topic
        ImportAttributes::importRelationsForExistingAttributes('number_has_genre', 'number_attribute', 'genre', 'number', 'attribute', 'number_id', 'genre_id',  1000);

        // imaginary
        ImportAttributes::importRelationsForExistingAttributes('number_has_imaginary', 'number_attribute', 'imaginary', 'number', 'attribute', 'number_id', 'imaginary_id',  1000);

        // musensemble
        ImportAttributes::importRelationsForExistingAttributes('number_has_musensemble', 'number_attribute', 'musensemble', 'number', 'attribute', 'number_id', 'musensemble_id',  1000);

        // musicalthesaurus
        ImportAttributes::importRelationsForExistingAttributes('number_has_musicalthesaurus', 'number_attribute', 'musical_thesaurus', 'number', 'attribute', 'number_id', 'musical_thesaurus_id',  1000);

        // quotationthesaurus
        ImportAttributes::importRelationsForExistingAttributes('number_has_quotationthesaurus', 'number_attribute', 'quotation_thesaurus', 'number', 'attribute', 'number_id', 'quotation_id',  1000);

        // source
        ImportAttributes::importRelationsForExistingAttributes('number_has_source', 'number_attribute', 'source_thesaurus', 'number', 'attribute', 'number_id', 'source_thesaurus_id',  1000);

        // stereotype
        ImportAttributes::importRelationsForExistingAttributes('number_has_stereotype', 'number_attribute', 'stereotype_thesaurus', 'number', 'attribute', 'number_id', 'stereotype_id',  1000);

        // numbers comments
        MigrationHelper::importAll('number','App\Component\Migration\ImportNumberComments::insert', 500);

        // thesaurus
        MigrationHelper::importAll('thesaurus','App\Component\Migration\ImportThesaurusComments::insert', 500);

        // songs attributes relations
        ImportAttributes::importRelationsForExistingAttributes('song_has_songtype', 'song_attribute', 'songtype', 'song', 'attribute', 'song_id', 'songtype_id',  1000);

        MySQLClean::finish();
        PgSQLClean::finish();

        $this->addFlash('success', 'Everything is ok');
        return $this->redirectToRoute('home');
    }

    /**
     * STEP 1
     *
     * @Route("/import/init", name="import_init")
     */
    public function init()
    {
        InitOperation::init();
        $this->addFlash('success', 'Initialisation ok');
        return $this->redirectToRoute('home');
    }

    /**
     * STEP 2
     *
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
        CleanOperation::clean(); // STEP 3
        $this->addFlash('success', 'DBs has been cleaned.');
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
        ImportAttributes::importRelationsForExistingAttributes('number_has_dancingtype', 'number_attribute', 'dancing_type', 'number', 'attribute', 'number_id', 'dancingtype_id',  1000);

        // diegeticplace
        ImportAttributes::importRelationsForExistingAttributes('number_has_diegeticplace', 'number_attribute', 'diegetic_place_thesaurus', 'number', 'attribute', 'number_id', 'diegetic_place_thesaurus_id',  1000);

        // exoticismthesaurus
        ImportAttributes::importRelationsForExistingAttributes('number_has_exoticismthesaurus', 'number_attribute', 'exoticism_thesaurus', 'number', 'attribute', 'number_id', 'exoticism_thesaurus_id',  1000);

        // genre => topic
        ImportAttributes::importRelationsForExistingAttributes('number_has_genre', 'number_attribute', 'genre', 'number', 'attribute', 'number_id', 'genre_id',  1000);

        // imaginary
        ImportAttributes::importRelationsForExistingAttributes('number_has_imaginary', 'number_attribute', 'imaginary', 'number', 'attribute', 'number_id', 'imaginary_id',  1000);

        // musensemble
        ImportAttributes::importRelationsForExistingAttributes('number_has_musensemble', 'number_attribute', 'musensemble', 'number', 'attribute', 'number_id', 'musensemble_id',  1000);

        // musicalthesaurus
        ImportAttributes::importRelationsForExistingAttributes('number_has_musicalthesaurus', 'number_attribute', 'musical_thesaurus', 'number', 'attribute', 'number_id', 'musical_thesaurus_id',  1000);

        // quotationthesaurus
        ImportAttributes::importRelationsForExistingAttributes('number_has_quotationthesaurus', 'number_attribute', 'quotation_thesaurus', 'number', 'attribute', 'number_id', 'quotation_id',  1000);

        // source
        ImportAttributes::importRelationsForExistingAttributes('number_has_source', 'number_attribute', 'source_thesaurus', 'number', 'attribute', 'number_id', 'source_thesaurus_id',  1000);

        // stereotype
        ImportAttributes::importRelationsForExistingAttributes('number_has_stereotype', 'number_attribute', 'stereotype_thesaurus', 'number', 'attribute', 'number_id', 'stereotype_id',  1000);

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

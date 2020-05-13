<?php

namespace App\Controller;

use App\Component\Migration\ImportFilms;
use App\Component\MySQL\Clean\MySQLClean;
use App\Component\MySQL\Import\MySQLImport;
use App\Component\MySQL\Initialization\MySQLInitialization;
use App\Component\PostgreSQL\Connection\PostgreSQLConnection;
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
        MySQLInitialization::init();
        MySQLImport::import('../data/mc2.sql');
        MySQLClean::clean();

        // import categories (from code)


        // import attributes (from thesaurus)

        // import films
        ImportFilms::importAll(500);

        // import films attributes

        // import numbers

        // import numbers attributes

        // import songs

        // import songs attributes

        // import all persons

        // import all distributor

        // import all studio


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
     * @Route("/import/upgrade", name="import_upgrade")
     */
    public function upgrade()
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/export", name="import_export")
     */
    public function export()
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/import/test", name="import_test")
     */
    public function test()
    {
        ImportFilms::importAll(500);

        return $this->redirectToRoute('home');
    }
    
}

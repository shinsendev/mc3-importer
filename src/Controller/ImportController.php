<?php

namespace App\Controller;

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
        MySQLInitialization::init();
        MySQLImport::import('../data/mc2.sql');
        MySQLClean::clean();

        $this->addFlash('success', 'Everything is ok');
        return $this->forward('App\Controller\DefaultController::index');
    }

    /**
     * @Route("/import/init", name="import_init")
     */
    public function init()
    {
        MySQLInitialization::init();

        $this->addFlash('success', 'Initialisation ok');
        return $this->forward('App\Controller\DefaultController::index');
    }

    /**
     * @Route("/import/creation", name="import_creation")
     */
    public function create()
    {
        MySQLImport::import('../data/mc2.sql');

        $this->addFlash('success', 'DB structure has been created and data has been imported');
        return $this->forward('App\Controller\DefaultController::index');
    }

    /**
     * @Route("/import/clean", name="import_clean")
     */
    public function clean()
    {
        MySQLClean::clean();
        $this->addFlash('success', 'DB has been cleaned.');
        return $this->forward('App\Controller\DefaultController::index');
    }

    /**
     * @Route("/import/upgrade", name="import_upgrade")
     */
    public function upgrade()
    {
        return $this->forward('App\Controller\DefaultController::index');
    }

    /**
     * @Route("/import/export", name="import_export")
     */
    public function export()
    {
        return $this->forward('App\Controller\DefaultController::index');
    }
    
}

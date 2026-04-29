<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('dashboard', 'Home::dashboard');
$routes->get('list', 'Home::list');
$routes->get('etudiants/(:any)', 'Home::notesEtudiant/$1');
$routes->get('etudiants','EtudiantController::index');

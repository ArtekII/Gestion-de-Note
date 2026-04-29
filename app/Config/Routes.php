<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('dashboard', 'Home::dashboard');
$routes->get('etudiants','EtudiantController::index');
$routes->get('list', 'EtudiantController::list');
$routes->get('etudiants/(:num)', 'EtudiantController::show/$1');

<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login-process', 'Auth::loginProcess');

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('auth/logout', 'Auth::logout');
    $routes->get('dashboard', 'EtudiantController::list', ['filter' => 'role:admin,etudiant']);
    $routes->get('dashboard/logout', 'Auth::logout');

    $routes->get('etudiants', 'EtudiantController::index', ['filter' => 'role:admin,etudiant']);
    $routes->get('list', 'EtudiantController::list', ['filter' => 'role:admin,etudiant']);
    $routes->get('etudiants/(:num)', 'EtudiantController::show/$1', ['filter' => 'role:admin,etudiant']);
    $routes->get('notes/create', 'EtudiantController::createNoteForm', ['filter' => 'role:admin']);
    $routes->post('notes/store', 'EtudiantController::storeNotes', ['filter' => 'role:admin']);
    $routes->get('notes/(:num)/edit', 'EtudiantController::editNoteForm/$1', ['filter' => 'role:admin']);
    $routes->post('notes/(:num)/update', 'EtudiantController::updateNote/$1', ['filter' => 'role:admin']);
    $routes->post('notes/(:num)/delete', 'EtudiantController::deleteNote/$1', ['filter' => 'role:admin']);
    $routes->post('etudiants/(:num)/notes/reset', 'EtudiantController::resetStudentNotes/$1', ['filter' => 'role:admin']);
});

<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
<<<<<<< HEAD
$routes->get('/', 'Auth::login');
$routes->get('/auth/login', 'Auth::login');
$routes->post('/auth/login-process', 'Auth::loginProcess');
$routes->get('/auth/register', 'Auth::register');
$routes->post('/auth/register-process', 'Auth::registerProcess');
$routes->get('/auth/logout', 'Auth::logout');

$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/dashboard/logout', 'Dashboard::logout');
=======
$routes->get('/', 'Home::index');
$routes->get('dashboard', 'Home::dashboard');
$routes->get('etudiants','EtudiantController::index');
$routes->get('list', 'EtudiantController::list');
$routes->get('etudiants/(:num)', 'EtudiantController::show/$1');
>>>>>>> e7a993526fcf0f70d23c9b95016ef80b1eb5e44d

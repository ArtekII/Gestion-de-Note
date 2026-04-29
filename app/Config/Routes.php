<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
$routes->get('/auth/login', 'Auth::login');
$routes->post('/auth/login-process', 'Auth::loginProcess');
$routes->get('/auth/register', 'Auth::register');
$routes->post('/auth/register-process', 'Auth::registerProcess');
$routes->get('/auth/logout', 'Auth::logout');

$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/dashboard/logout', 'Dashboard::logout');

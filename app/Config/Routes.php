<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Home::toFormLogin');
$routes->post('/login', 'AuthController::login');

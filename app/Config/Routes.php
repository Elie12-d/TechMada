<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers;

/**
 * @var RouteCollection $routes
 */

// Public routes
$routes->get('/', 'Home::toFormLogin');
$routes->post('/login', 'AuthController::login');


// Protected routes (require authentication)
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/dashboard', 'DashboardController::index');
    $routes->get('/conges', 'CongeController::index');
    $routes->get('/conges/create', 'CongeController::create');
    $routes->post('/conges/store', 'CongeController::store');
    $routes->get('/employes', 'EmployerController::index');
});

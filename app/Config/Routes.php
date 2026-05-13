<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers;

$routes->get('/login', 'AuthController::login');

$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/dashboard', 'DashboardController::index');
    $routes->get('/conges', 'CongeController::index');
    $routes->get('/conges/create', 'CongeController::create');
    $routes->post('/conges/store', 'CongeController::store');
    // $routes->get('/employes', 'EmployerController::index');
    
});
// $routes->get();
/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Home::toFormLogin');
$routes->post('/login', 'AuthController::login');

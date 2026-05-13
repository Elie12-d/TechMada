<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers;

/**
 * @var RouteCollection $routes
 */

// Public routes
$routes->get('/', 'Home::toFormLogin');
$routes->post('/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');

// Protected routes (require authentication)
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/dashboard', 'DashboardController::index');
    $routes->get('/conges', 'CongeController::index');
    $routes->get('/conges/create', 'CongeController::create');
    $routes->post('/conges/store', 'CongeController::store');
    $routes->get('/employes', 'EmployerController::index');
});

// Admin routes (require admin role)
$routes->group('admin', ['filter' => 'AdminFilter'], function($routes) {
    $routes->get('/dashboard', 'AdminController::dashboard');
    $routes->get('/employes', 'AdminController::employes');
    $routes->post('/employes/store', 'AdminController::store');
    $routes->get('/employes/edit/(:num)', 'AdminController::edit/$1');
    $routes->post('/employes/update/(:num)', 'AdminController::update/$1');
    $routes->post('/employes/delete/(:num)', 'AdminController::delete/$1');
});

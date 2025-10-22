<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->resource('certieye', [
    'controller' => 'CertiEyeController',
    'filter' => 'cors'
]);
$routes->options('certieye', 'CertiEyeController::index', ['filter' => 'cors']);

$routes->get('pricechange', 'PriceChangesController::index', ['filter' => 'cors']);
$routes->get('crawl', 'PriceChangesController::show/$1', ['filter' => 'cors']);
$routes->resource('pricechanges', [
    'controller' => 'PriceChangesController',
    'filter' => 'auth'
]);
$routes->options('pricechanges', 'PriceChangesController::index', ['filter' => 'cors']);
$routes->options('pricechanges/(:segment)', 'PriceChangesController::index', ['filter' => 'cors']);

$routes->resource('prices', [
    'controller' => 'PricesController',
    'filter' => 'auth'
]);
$routes->options('prices', 'PricesController::index', ['filter' => 'cors']);
$routes->options('prices/(:segment)', 'PricesController::index', ['filter' => 'cors']);

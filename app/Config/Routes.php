<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->resource('certieye', [
    'controller' => 'CertiEyeController',
    'filter' => 'cors'
]);

$routes->resource('pricechanges', [
    'controller' => 'PriceChangesController',
    'filter' => 'auth'
]);

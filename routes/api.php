<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/


$router->get('/', function () {
    return [
        "message"   => "Welcome to the InDigital Challenge API",
        "version"   => "1.0.0",
        "environment" => app()->environment()
    ];
});


$router->post('creacliente', 'ClientController@index');

$router->get('listclientes', 'ClientController@list');

$router->get('kpideclientes', 'PerformanceController@index');

$router->get('job/kpi', 'JobController@kpi');

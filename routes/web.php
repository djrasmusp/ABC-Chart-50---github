<?php

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function() use ($router) {
    // Get Chart data
   $router->get('chart', ['uses' => 'ChartsController@current_chart']);
   $router->get('chart/{id:[0-9]+}', ['uses' => 'ChartsController@selected_chart']);

   // get Track data
    $router->get('tracks', ['uses' => 'TracksController@get_tracks']);

    // get Spotify data
    $router->get('spotify/{search}', ['uses' => 'TracksController@get_spotify']);
});


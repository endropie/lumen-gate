<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    return [
        'name' => env('APP_NAME', 'Lumen'),
        'lumen' => $router->app->version()
    ];
});

$router->group(['prefix' => 'api'], function () use ($router) {


    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->get('/user', function () use ($router) {
            return auth()->user();
        });
    });
});

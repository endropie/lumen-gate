<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    return [
        'name' => env('APP_NAME', 'Lumen'),
        'lumen' => $router->app->version()
    ];
});

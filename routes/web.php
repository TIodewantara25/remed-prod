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

$router->get('/', function () use ($router) {
    return $router->app->version();
});
    $router->get('/stuff', 'StuffController@index');

    $router->post('/login', 'UserController@login');
    $router->get('/logout', 'UserController@logout');
    $router->get('/addstock', 'StuffStockController@addstock');
    
//stuff 
//struktur :$route→httpmethod('/path', 'NamaController@method');
$router->group(['prefix' => 'stuff','middleware' => 'auth'],function() use ($router) {
    //static routes 
    $router->get('/data', 'StuffController@index');
    $router->post('/', 'StuffController@store');
    $router->get('/trash', 'StuffController@trash');

    //dynamic routes
    $router->get('{id}', 'StuffController@show');
    $router->patch('{id}', 'StuffController@update');
    $router->delete('/{id}', 'StuffController@destroy');
    $router->get('/restore/{id}', 'StuffController@restore');
    $router->get('/Permanent/{id}', 'StuffController@deletPermanent');


});
// ,'middleware' => 'auth'
$router->group(['prefix' => '/user'],function() use ($router) {
    $router->get('/data', 'UserController@index');
    $router->post('/', 'UserController@store');
    $router->get('/trash', 'UserController@trash');

    $router->get('{id}', 'UserController@show');
    $router->patch('{id}', 'UserController@update');
    $router->delete('/{id}', 'UserController@destroy');
    $router->get('/restore/{id}', 'UserController@restore');
    $router->get('/Permanent/{id}', 'UserController@deletPermanent');

});
$router->group(['prefix' => 'inbound-stuff', 'middleware' => 'auth'],function() use ($router) {
    $router->get('/', 'InboundStuffController@index');
    $router->post('store', 'InboundStuffController@store');

    $router->get('detail/{id}', 'InboundStuffController@show');
    $router->patch('update/{id}', 'InboundStuffController@update');
    $router->delete('delete/{id}', 'InboundStuffController@destroy');
    $router->get('recycle-bin', 'InboundStuffController@recycle-bin');
    $router->get('/restore/{id}', 'InboundStuffController@restore');    
    $router->get('force-delete/{id}', 'InboundStuffController@forceDestroy');

});
$router->group(['prefix' => 'stuff-stock', 'middleware' => 'auth'],function() use ($router) {
    $router->get('/data', 'StuffStockController@index');
    $router->post('store', 'StuffStockController@store');

    // $router->get('detail/{id}', 'InboundStuffController@show');
    // $router->patch('update/{id}', 'InboundStuffController@update');
    $router->delete('delete/{id}', 'StuffStockController@destroy');
    // $router->get('recycle-bin', 'InboundStuffController@recycle-bin');
    $router->get('/restore/{id}', 'StuffStockController@restore');    
    // $router->get('force-delete/{id}', 'InboundStuffController@forceDestroy');
    $router->post('add-stock/{id}', 'StuffStockController@addstock');

});


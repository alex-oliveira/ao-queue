<?php

$config = [
    'namespace' => 'AoQueue\Http\Controllers',
    'prefix' => 'api/ao-queue',
    'as' => 'api.ao-queue.',
    'middleware' => ['api']
];

Route::group($config, function () {

    Route::get('/dashboard', ['as' => 'dashboard', 'uses' => 'DashboardController@index']);

});
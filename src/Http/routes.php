<?php

$config = [
    'namespace' => 'AoQueue\Http\Controllers',
    'prefix' => 'ao-queue',
    'middleware' => ['api']
];

Route::group($config, function () {

    // Route::get('dashboard', ['uses' => 'DashboardController@index']);

});
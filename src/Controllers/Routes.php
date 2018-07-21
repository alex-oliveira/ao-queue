<?php

$config = config('ao-queue.routes.config');

if (empty($config)) {
    return null;
}

Route::group($config, function () {

    // Route::get('dashboard', ['uses' => 'DashboardController@index']);

});
<?php

namespace AoQueue;

use AoQueue\Console\RestartCommand;
use AoQueue\Console\RunCommand;
use AoQueue\Console\ScreenCommand;
use AoQueue\Console\StartCommand;
use AoQueue\Console\StopCommand;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ao-queue');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
                __DIR__ . '/../database/seeds' => database_path('seeds'),
            ], 'ao-queue');
        }

        $this->commands([
            RestartCommand::class,
            RunCommand::class,
            ScreenCommand::class,
            StartCommand::class,
            StopCommand::class,
        ]);
    }

    public function register()
    {
        $this->app->singleton('AoQueue', function () {
            return new Tools();
        });

        require_once(__DIR__ . '/Utils/helpers.php');
    }

}
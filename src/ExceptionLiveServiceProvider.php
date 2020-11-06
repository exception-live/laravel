<?php

namespace ExceptionLive\ExceptionLiveLaravel;

use ExceptionLive\ExceptionLive;
use ExceptionLive\ExceptionLiveLaravel\Commands\ExceptionLiveCheckinCommand;
use ExceptionLive\ExceptionLiveLaravel\Commands\ExceptionLiveDeployCommand;
use ExceptionLive\ExceptionLiveLaravel\Commands\ExceptionLiveInstallCommand;
use ExceptionLive\ExceptionLiveLaravel\Commands\ExceptionLiveTestCommand;
use ExceptionLive\ExceptionLiveLaravel\Contracts\Installer as InstallerContract;
use GuzzleHttp\Client;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Support\ServiceProvider;

class ExceptionLiveServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->bindCommands();
            $this->registerCommands();
            $this->app->bind(InstallerContract::class, Installer::class);

            $this->publishes([
                __DIR__.'/../config/exceptionlive.php' => base_path('config/exceptionlive.php'),
            ], 'config');
        }

        $this->registerMacros();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/exceptionlive.php', 'exceptionlive');

        $this->app->singleton(ExceptionLive::class, function ($app) {
            return (new ExceptionLiveLaravel())->make($app['config']['exceptionlive']);
        });

        $this->app->alias(ExceptionLive::class, 'exceptionlive');

        $this->app->singleton('exceptionlive.isLumen', function () {
            return preg_match('/lumen/i', $this->app->version());
        });

        $this->app->when(ExceptionLiveDeployCommand::class)
            ->needs(Client::class)
            ->give(function () {
                return new Client([
                    'http_errors' => false,
                ]);
            });
    }

    /**
     * @return void
     */
    private function registerCommands()
    {
        $this->commands([
            'command.exceptionlive:test',
            'command.exceptionlive:checkin',
            'command.exceptionlive:install',
            'command.exceptionlive:deploy',
        ]);
    }

    /**
     * @return void
     */
    private function bindCommands()
    {
        $this->app->bind(
            'command.exceptionlive:test',
            ExceptionLiveTestCommand::class
        );

        $this->app->bind(
            'command.exceptionlive:checkin',
            ExceptionLiveCheckinCommand::class
        );

        $this->app->bind(
            'command.exceptionlive:install',
            ExceptionLiveInstallCommand::class
        );

        $this->app->bind(
            'command.exceptionlive:deploy',
            ExceptionLiveDeployCommand::class
        );
    }

    /**
     * @return void
     */
    private function registerMacros()
    {
        Event::macro('thenPingExceptionLive', function ($id) {
            return $this->then(function () use ($id) {
                app(ExceptionLive::class)->checkin($id);
            });
        });

        Event::macro('pingExceptionLiveOnSuccess', function ($id) {
            return $this->onSuccess(function () use ($id) {
                app(ExceptionLive::class)->checkin($id);
            });
        });
    }
}

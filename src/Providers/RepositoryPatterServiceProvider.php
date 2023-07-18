<?php

namespace Microoculus\LaravelRepositoryPattern\Providers;

use Illuminate\Support\ServiceProvider;
use Microoculus\LaravelRepositoryPattern\Commands\RepositoryPattern;

class RepositoryPatterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
//        $this->mergeConfigFrom(__DIR__.'/config/RepositoryConfig.php','RepositoryPattern');

        $this->loadViewsFrom(__DIR__.'/../resources/stubs', 'RepositoryPattern');

        $this->publishes([
            __DIR__.'/../resources/stubs' => resource_path('vendor/laravel-repository-pattern/stubs'),
//            __DIR__.'/config/RepositoryConfig.php' => config_path('RepositoryConfig.php'),

        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                 __DIR__.'/../resources/stubs' => resource_path('vendor/laravel-repository-pattern/stubs')
                ]);
        }

        // $this->commands([
        //     RepositoryPattern::class,
        // ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                RepositoryPattern::class,
            ]);
        }
    }
}

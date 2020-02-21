<?php

namespace nabilanam\SimpleUpload;

use Illuminate\Support\ServiceProvider;

class SimpleUploadServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'nabilanam');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'nabilanam');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/simpleupload.php', 'simpleupload');

        // Register the service the package provides.
        $this->app->singleton('simpleupload', function ($app) {
            return new SimpleUpload;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['simpleupload'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/simpleupload.php' => config_path('simpleupload.php'),
        ], 'simpleupload.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/nabilanam'),
        ], 'simpleupload.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/nabilanam'),
        ], 'simpleupload.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/nabilanam'),
        ], 'simpleupload.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}

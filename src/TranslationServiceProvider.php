<?php

namespace Alaaeta\Translation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{

    public function boot()
    {
        if ($this->app->runningInConsole()){
            $this->registerPublishing();
        }

        $this->registerMigrations();
        $this->registerFacades();


        Blade::directive('t', function ($args) {

           return $this->app->make('translation')->translate(preg_replace('~^[\'"]?(.*?)[\'"]?$~', '$1', $args));
        });
    }

    public function register()
    {

        // Bind translation to the IoC.
        $this->app->bind('translation', function ($app) {
            return new Translation($app);
        });


        include __DIR__.'/helpers.php';
    }

    protected function registerPublishing()
    {
        $this->publishes([
            __DIR__.'/../config/translation.php' => config_path('translation.php')
        ],'translation-config');
    }


    private function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function registerFacades()
    {
        $this->app->singleton('Translation' , function ($app){
            return new \Alaaeta\Translation\Translation();
        });
    }

}

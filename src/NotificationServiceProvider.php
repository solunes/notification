<?php

namespace Solunes\Notification;

use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function boot() {
        /* Publicar Elementos */
        $this->publishes([
            __DIR__ . '/config' => config_path()
        ], 'config');

        /* Cargar Traducciones */
        $this->loadTranslationsFrom(__DIR__.'/lang', 'notification');

        /* Cargar Vistas */
        $this->loadViewsFrom(__DIR__ . '/views', 'notification');
    }


    public function register() {
        /* Registrar ServiceProvider Internos */
        //$this->app->register('Rossjcooper\LaravelHubSpot\HubSpotServiceProvider');

        /* Registrar Alias */
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        //$loader->alias('HubSpot', 'Rossjcooper\LaravelHubSpot\Facades\HubSpot');

        $loader->alias('Notification', '\Solunes\Notification\App\Helpers\Notification');

        /* Comandos de Consola */
        $this->commands([
            \Solunes\Notification\App\Console\TestEmail::class,
            \Solunes\Notification\App\Console\TestSms::class,
        ]);

        $this->mergeConfigFrom(
            __DIR__ . '/config/notification.php', 'notification'
        );
    }
    
}

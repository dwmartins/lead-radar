<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //----------------------------
        // Local da aplicação
        //----------------------------
        $locale = 'pt_BR';

        if ($this->app->runningInConsole() === false && request()->hasHeader('Accept-Language')) {
            $acceptLanguage = request()->header('Accept-Language');
            $shortLocale = explode(',', $acceptLanguage)[0];
            $shortLocale = str_replace('-', '_', $shortLocale);
            
            // Mapeamento frontend → backend
            $localeMap = [
                'pt' => 'pt_BR',
                'pt_BR' => 'pt_BR',
                'en' => 'en',
                'en_US' => 'en',
            ];
            
            $locale = $localeMap[$shortLocale] ?? 'pt_BR';
        }

        App::setLocale($locale);

        //----------------------------
        // Local da aplicação
        //----------------------------
        $timezone = Config::get('app.timezone', 'America/Sao_Paulo');
        date_default_timezone_set($timezone);
        Carbon::setLocale($locale);

        //----------------------------
        // Formato e data e hora
        //----------------------------
        Config::set('app.date_format', 'd/m/Y');
        Config::set('app.datetime_format', 'd/m/Y H:i:s');

        //----------------------------
        // Configurações globais
        //----------------------------
        Config::set('app.currency', 'BRL');
    }
}

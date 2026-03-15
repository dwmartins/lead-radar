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
        Carbon::setLocale($locale);

        //----------------------------
        // Local da aplicação
        //----------------------------
        date_default_timezone_set('UTC');
        Config::set('app.timezone', 'UTC');

        //----------------------------
        // Formato e data e hora, baseados no locale
        //----------------------------
        $formats = [
            'pt_BR' => [
                'date' => 'd/m/Y',
                'datetime' => 'd/m/Y H:i:s',
                'currency' => 'BRL'
            ],
            'en' => [
                'date' => 'm/d/Y',
                'datetime' => 'm/d/Y h:i A',
                'currency' => 'USD'
            ]
        ];

        $format = $formats[$locale] ?? $formats['pt_BR'];

        Config::set('app.date_format', $format['date']);
        Config::set('app.datetime_format', $format['datetime']);
        Config::set('app.currency', $format['currency']);
    }
}

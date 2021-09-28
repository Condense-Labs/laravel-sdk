<?php

namespace Condense;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class CondenseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/condense.php', 'condense');

        $this->app->bind(CondenseClient::class, function ($app) {
            $http = new Client([
                'base_uri' => $app['config']->get('condense.host', 'https://app.condense.dev').'/api/',
                'headers' => [
                    'Authorization' => 'Bearer '.$app['config']->get('condense.api_key'),
                ]
            ]);

            return new CondenseClient($http);
        });
    }

    public function boot() {
        $this->publishes([
            __DIR__ . '/config/condense.php' => config_path('condense.php'),
        ]);
    }
}

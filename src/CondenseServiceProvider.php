<?php

namespace Condense;

use GuzzleHttp\Client;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\ServiceProvider;
use Monolog\Handler\BufferHandler;

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

        $this->app->singleton('condense.handler', fn () => new BufferHandler(
            $this->app->make(CondenseHandler::class)
        ));
    }

    public function boot() {
        $this->publishes([
            __DIR__ . '/config/condense.php' => config_path('condense.php'),
        ]);

        $this->app->make('events')->listen(CommandStarting::class, function (CommandStarting $event) {
            $this->app->singleton('requestId', fn () => Uuid::uuid4()->toString());

            $this->app->make('log')->debug(
                'command starting',
                [
                    '_condense_type' => 'command.start',
                    'command' => $event->command,
                ],
            );
        });

        $this->app->make('events')->listen(
            CommandFinished::class,
            fn (CommandFinished $event) => $this->app->make('log')->debug(
                'command completed',
                [
                    'command' => $event->command,
                    'exit_code' => $event->exitCode,
                ]
            )
        );
    }
}

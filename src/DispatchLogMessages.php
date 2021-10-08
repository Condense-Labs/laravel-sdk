<?php

namespace Condense;

class DispatchLogMessages
{
    public function handle($event)
    {
        $logger = $event->app->make('log')->channel('condense');
        $logger->getHandlers()[0]->flush();
    }
}
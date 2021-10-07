<?php

namespace Condense;

use Monolog\Handler\BufferHandler;
use Monolog\Logger;

class CondenseLogger
{
    public function __invoke()
    {
        return new Logger('condense', [
            new BufferHandler(
                app(CondenseHandler::class)
            )
        ]);
    }
}

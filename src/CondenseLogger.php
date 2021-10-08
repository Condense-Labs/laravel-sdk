<?php

namespace Condense;

use Monolog\Handler\BufferHandler;
use Monolog\Logger;

class CondenseLogger
{
    public function __invoke()
    {
        return tap(new Logger('condense', [
            app('condense.handler')
        ]))->pushProcessor(new CondenseProcessor);
    }
}

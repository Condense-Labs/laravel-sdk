<?php

namespace Condense;

class ProcessorTap
{
    /**
     * @param \Monolog\Logger $logger
     */
    public function __invoke($logger)
    {
        $logger->pushProcessor(new CondenseProcessor());
    }
}

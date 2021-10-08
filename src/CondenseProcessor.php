<?php

namespace Condense;

use Illuminate\Support\Facades\Auth;

class CondenseProcessor
{
    public function __invoke(array $record)
    {
        $context = null;

        if (app()->runningInConsole()) {
            $context = 'artisan';
        }

        if (app()->bound('request')) {
            $context = 'http';
        }

        $record['extra'] = array_merge(
            $record['extra'],
            [
                'condense' => [
                    'request_id' => app()->bound('requestId') ? app('requestId') : null,
                    'user_id' => Auth::id(),
                    'project' => config('condense.project'),
                    'action' => app()->bound('condense.action') ? app('condense.action') : null,
                    'context' => $context,
                ],
            ],
        );

        return $record;
    }
}

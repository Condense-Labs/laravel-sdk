<?php

namespace Condense;

use Illuminate\Support\Facades\Auth;

class CondenseProcessor
{
    public function __invoke(array $record)
    {
        $record['extra'] = array_merge(
            $record['extra'],
            [
                'condense' => [
                    'request_id' => app()->bound('requestId') ? app('requestId') : null,
                    'user_id' => Auth::id(),
                    'project' => config('condense.project'),
                ],
            ],
        );

        return $record;
    }
}

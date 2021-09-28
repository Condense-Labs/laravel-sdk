<?php

namespace Condense;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class CondenseClient
{
    public function __construct(protected Client $http) {}

    public function record(array $record)
    {
        $this->http->post('logs', [
            'json' => $record,
        ]);
    }

    public function recordRaw(string $body)
    {
        $this->http->post('logs', [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => $body,
        ]);
    }
}

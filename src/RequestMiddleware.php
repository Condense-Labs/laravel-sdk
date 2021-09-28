<?php

namespace Condense;

use Decahedron\StickyLogging\StickyContext;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class RequestMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        app()->singleton('requestId', function () {
            return Uuid::uuid4()->toString();
        });

        Log::debug('request started', [
            '_condense_type' => 'request.start',
            'request' => [
                'path' => $request->path(),
                'method' => $request->method(),
                'data' => $request->input(),
                'headers' => $request->headers->all(),
            ]
        ]);

        /** @var Response $response */
        $response = $next($request);

        Log::debug('request completed', [
            '_condense_type' => 'request.complete',
            'route_path' => $request->route()?->uri(),
            'response' => [
                'status' => $response->status(),
                'headers' => $response->headers->all(),
            ],
        ]);

        return $response;
    }
}

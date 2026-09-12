<?php

declare(strict_types=1);

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Kimulisiraj\SmsSpeedaMobile\Api\Client;

/**
 * @param array<int, mixed> $responses
 * @param array<int, array<string, mixed>> $history
 */
function apiClient(array $responses, array &$history = []): Client
{
    $stack = HandlerStack::create(new MockHandler($responses));
    $stack->push(Middleware::history($history));

    return new Client(new GuzzleClient(['handler' => $stack]), 'api-key', 'api-secret');
}

/**
 * @param array<int, array<string, mixed>> $history
 *
 * @return array<string, string>
 */
function lastQuery(array $history): array
{
    parse_str($history[array_key_last($history)]['request']->getUri()->getQuery(), $query);

    return $query;
}

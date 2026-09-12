<?php

declare(strict_types=1);

use Kimulisiraj\SmsSpeedaMobile\Api\SendResponse;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\SendException;

it('throws when the send response is missing a required key', function (): void {
    expect(fn (): SendResponse => SendResponse::fromResponse('{"remarks":"ok","status":"S"}'))
        ->toThrow(SendException::class, 'missing the `message_id` key');
});

it('throws when the send response is not a json object', function (): void {
    expect(fn (): SendResponse => SendResponse::fromResponse('"just a string"'))
        ->toThrow(SendException::class, 'not a JSON object');
});

it('throws on malformed json', function (): void {
    expect(fn (): SendResponse => SendResponse::fromResponse('{not json'))
        ->toThrow(JsonException::class);
});

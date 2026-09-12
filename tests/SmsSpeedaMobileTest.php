<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use Kimulisiraj\SmsSpeedaMobile\SmsSpeedaMobile;

it('sends the chained recipient and message', function (): void {
    $history = [];
    $client = apiClient([new Response(200, [], '{"remarks":"ok","message_id":"1","status":"S"}')], $history);

    $sms = new SmsSpeedaMobile('api-key', 'api-secret', $client);

    $response = $sms->message('Hello, Kimulisiraj!')
        ->to('256781234567')
        ->send();

    expect(lastQuery($history))->toMatchArray([
        'phonenumber' => '256781234567',
        'textmessage' => 'Hello, Kimulisiraj!',
    ]);

    expect($response->getStatus())->toBe('OK');
});

it('prefers explicit send arguments over chained state', function (): void {
    $history = [];
    $client = apiClient([new Response(200, [], '{"remarks":"ok","message_id":"1","status":"S"}')], $history);

    $sms = new SmsSpeedaMobile('api-key', 'api-secret', $client);

    $sms->to('256700000000')
        ->message('Chained')
        ->send('256781234567', 'Explicit');

    expect(lastQuery($history))->toMatchArray([
        'phonenumber' => '256781234567',
        'textmessage' => 'Explicit',
    ]);
});

it('sends the chained message id when querying status', function (): void {
    $history = [];
    $client = apiClient([new Response(200, [], '{"message_id":"4234"}')], $history);

    $sms = new SmsSpeedaMobile('api-key', 'api-secret', $client);

    $response = $sms->messageId('4234')->messageStatus();

    expect(lastQuery($history))->toMatchArray(['message_id' => '4234'])
        ->and($response->getMessageId())->toBe('4234');
});

it('builds a configured instance that supports chaining', function (): void {
    expect(SmsSpeedaMobile::config('api-key', 'api-secret'))
        ->toBeInstanceOf(SmsSpeedaMobile::class);
});

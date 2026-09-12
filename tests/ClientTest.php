<?php

declare(strict_types=1);

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Kimulisiraj\SmsSpeedaMobile\Api\MessageStatusResponse;
use Kimulisiraj\SmsSpeedaMobile\Api\SendRequest;
use Kimulisiraj\SmsSpeedaMobile\Api\SendResponse;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\InvalidMessageException;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\InvalidNumberException;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\SendException;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\SmsBroadcastException;

it('parses a successful send response', function (): void {
    $client = apiClient([
        new Response(200, [], json_encode([
            'remarks' => 'Message sent successfully',
            'message_id' => 12345,
            'status' => 'S',
        ])),
    ]);

    $response = $client->send('256781234567', 'Hello');

    expect($response->getStatus())->toBe('OK')
        ->and($response->hasError())->toBeFalse()
        ->and($response->getMessageId())->toBe('12345')
        ->and($response->getMessage())->toBe('Message sent successfully')
        ->and($response->getError())->toBeNull();
});

it('flags a failed send response', function (): void {
    $client = apiClient([
        new Response(200, [], json_encode([
            'remarks' => 'Insufficient balance',
            'message_id' => '',
            'status' => 'F',
        ])),
    ]);

    $response = $client->send('256781234567', 'Hello');

    expect($response->getStatus())->toBe('FAILED')
        ->and($response->hasError())->toBeTrue()
        ->and($response->getError())->toBe('Insufficient balance');
});

it('sends credentials and payload as query parameters', function (): void {
    $history = [];
    $client = apiClient([new Response(200, [], '{"remarks":"ok","message_id":"1","status":"S"}')], $history);

    $client->send('256781234567', 'Hello');

    expect(lastQuery($history))
        ->toMatchArray([
            'api_id' => 'api-key',
            'api_password' => 'api-secret',
            'phonenumber' => '256781234567',
            'textmessage' => 'Hello',
            'sender_id' => SendRequest::DEFAULT_SENDER_ID,
            'encoding' => SendRequest::ENCODING_TEXT,
        ]);
});

it('sends credentials when querying message status', function (): void {
    $history = [];
    $client = apiClient([new Response(200, [], '{"message_id":"1"}')], $history);

    $client->messageStatus('1');

    expect(lastQuery($history))->toMatchArray([
        'message_id' => '1',
        'api_id' => 'api-key',
        'api_password' => 'api-secret',
    ]);
});

it('validates the destination number before hitting the api', function (): void {
    $history = [];
    $client = apiClient([new Response(200, [], '{}')], $history);

    expect(fn (): SendResponse => $client->send('0783211244', 'Hello'))
        ->toThrow(InvalidNumberException::class);

    expect($history)->toBeEmpty();
});

it('rejects an empty message', function (): void {
    $client = apiClient([new Response(200, [], '{}')]);

    expect(fn (): SendResponse => $client->send('256781234567', ''))
        ->toThrow(InvalidMessageException::class, 'Message is empty');
});

it('rejects a message longer than the standard length', function (): void {
    $client = apiClient([new Response(200, [], '{}')]);

    expect(fn (): SendResponse => $client->send('256781234567', str_repeat('a', 161)))
        ->toThrow(InvalidMessageException::class);
});

it('counts multibyte characters rather than bytes', function (): void {
    $client = apiClient([new Response(200, [], '{"remarks":"ok","message_id":"1","status":"S"}')]);

    $response = $client->send('256781234567', str_repeat('é', 160));

    expect($response->getStatus())->toBe('OK');
});

it('requires a message id before querying status', function (): void {
    $client = apiClient([new Response(200, [], '{}')]);

    expect(fn (): MessageStatusResponse => $client->messageStatus(''))
        ->toThrow(InvalidMessageException::class, 'No `messageId` provided');
});

it('parses a message status response without carbon', function (): void {
    $client = apiClient([
        new Response(200, [], json_encode([
            'message_id' => '4234',
            'PhoneNumber' => '256781234567',
            'SMSMessage' => 'Hello',
            'MessageType' => 'Text',
            'MessageLength' => '5',
            'MessageParts' => '1',
            'ClientCost' => '32.5',
            'DLRStatus' => 'Delivered',
            'SMSID' => 'abc-1',
            'ErrorCode' => null,
            'ErrorDescription' => null,
            'SentDateUTC' => '2026-01-15T09:30:00Z',
            'Remarks' => 'Submitted',
        ])),
    ]);

    $response = $client->messageStatus('4234');

    expect($response->getMessageId())->toBe('4234')
        ->and($response->getMessageLength())->toBe(5)
        ->and($response->getMessageParts())->toBe(1)
        ->and($response->getMessageCost())->toBe(32.5)
        ->and($response->getDeliveryStatus())->toBe('Delivered')
        ->and($response->getErrorCode())->toBeNull()
        ->and($response->getSentDateTime())->toBe('2026-01-15 09:30:00')
        ->and($response->getRemarks())->toBe('Submitted');
});

it('returns null for an unparsable sent date', function (): void {
    $client = apiClient([new Response(200, [], '{"message_id":"1","SentDateUTC":"not-a-date"}')]);

    expect($client->messageStatus('1')->getSentDateTime())->toBeNull();
});

it('returns the balance payload', function (): void {
    $client = apiClient([new Response(200, [], '{"BalanceAmount":1000,"CurrenceCode":"UGX"}')]);

    expect($client->getBalance())
        ->toHaveKey('BalanceAmount')
        ->toHaveKey('CurrenceCode');
});

it('wraps connection failures in a send exception', function (): void {
    $client = apiClient([
        new ConnectException('cURL error 28: timeout', new Request('GET', 'http://example.test')),
    ]);

    expect(fn (): SendResponse => $client->send('256781234567', 'Hello'))
        ->toThrow(SendException::class, 'Failed to send SMS');
});

it('surfaces the api error body on a bad response', function (): void {
    $client = apiClient([
        new ClientException(
            'Client error',
            new Request('GET', 'http://example.test'),
            new Response(404, [], '{"StatusCode":404,"ReasonPhrase":"Missing Parameters"}'),
        ),
    ]);

    expect(fn (): array => $client->getBalance())
        ->toThrow(SmsBroadcastException::class, 'Missing Parameters');
});

it('redacts the api password from failure messages', function (): void {
    $client = apiClient([
        new ConnectException(
            'cURL error 28 for http://example.test/api/SendSMS?api_id=api-key&api_password=super-secret',
            new Request('GET', 'http://example.test'),
        ),
    ]);

    try {
        $client->send('256781234567', 'Hello');
    } catch (SendException $sendException) {
        expect($sendException->getMessage())
            ->not->toContain('super-secret')
            ->toContain('api_password=***');

        return;
    }

    $this->fail('Expected a SendException to be thrown.');
});

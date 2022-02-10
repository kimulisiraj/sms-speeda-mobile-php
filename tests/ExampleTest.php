<?php

use Kimulisiraj\SmsSpeedaMobile\Api\SendResponse;
use Kimulisiraj\SmsSpeedaMobile\SmsSpeedaMobile;

it('can test', function () {

    $mock = mock(SmsSpeedaMobile::class)->expect(
        getBalance: fn () => ['BalanceAmount' => 1000, 'CurrenceCode' => 'UGX'],
    );
    $response = $mock->getBalance();

    expect($response)->toHaveKey('BalanceAmount');
    expect($response)->toHaveKey('CurrenceCode');
});


it('can send an sms', function () {
    $mock = mock(SmsSpeedaMobile::class)->expect(
        send: fn ($to, $message) => new SendResponse($message, 123, SendResponse::SUCCESS),
    );

    $response = $mock->send(
        to: 256783211244,
        message: 'Testing link'
    );

    expect($response->getStatus())->toBe('OK');
    expect($response->getMessage())->toBe('Testing link');
    expect($response->hasError())->toBeFalse();
    expect($response->getMessageId())->toBe('123');
});

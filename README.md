# Send SMS using speed mobile api

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kimulisiraj/sms-speeda-mobile-php.svg?style=flat-square)](https://packagist.org/packages/kimulisiraj/sms-speeda-mobile-php)
[![Tests](https://github.com/kimulisiraj/sms-speeda-mobile-php/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/kimulisiraj/sms-speeda-mobile-php/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/kimulisiraj/sms-speeda-mobile-php.svg?style=flat-square)](https://packagist.org/packages/kimulisiraj/sms-speeda-mobile-php)

A small PHP client for the [Speeda Mobile](http://apidocs.speedamobile.com) SMS API. It sends single messages, queries
delivery reports and reads your account balance, with the request validation and response parsing handled for you.

## Requirements

- PHP 8.2 or higher
- Guzzle 7.9 or 8.x

## Installation

You can install the package via composer:

```bash
composer require kimulisiraj/sms-speeda-mobile-php
```

## Usage

### Send single message

```php
use Kimulisiraj\SmsSpeedaMobile\SmsSpeedaMobile;

$client = new SmsSpeedaMobile(
    apiKey: 'your-username',
    apiSecret: 'your-password',
);

$response = $client->send(
    to: '256781234567',
    message: 'Hello, Kimulisiraj!',
);
```

Destination numbers must be in international format for Uganda, Kenya or Tanzania (`256`, `254` or `255` followed by
nine digits). Messages are limited to 160 characters.

### From the response you can access:

```php
$response->getStatus();    // OK | FAILED
$response->getMessage();   // Message sent successfully
$response->hasError();     // true | false
$response->getError();     // the failure reason, or null
$response->getMessageId(); // I.E 12345
```

### Chaining

```php
use Kimulisiraj\SmsSpeedaMobile\SmsSpeedaMobile;

$client = SmsSpeedaMobile::config(
    apiKey: 'your-api-key',           // e.g. env('SPEEDAMOBILE_SMS_API_ID')
    apiPassword: 'your-api-password', // e.g. env('SPEEDAMOBILE_SMS_API_PASSWORD')
);

$response = $client->message('Hello, Kimulisiraj!')
    ->to('256781234567')
    ->send();
```

### Query Delivery Report / Message Status

```php
use Kimulisiraj\SmsSpeedaMobile\SmsSpeedaMobile;

$client = new SmsSpeedaMobile(
    apiKey: 'your-username',
    apiSecret: 'your-password',
);

$response = $client->messageStatus(
    messageId: '4234', // Message ID you received at the time of submit
);
```

### From the response you can access:

```php
$response->getMessageId();        // string  Message ID of the request
$response->getPhoneNumber();      // string  Phone number the message was sent to
$response->getMessageBody();      // string  Text of the SMS message
$response->getMessageType();      // string  Message encoding
$response->getMessageLength();    // int     Length of the message
$response->getMessageParts();     // int     Number of message parts
$response->getMessageCost();      // float   Amount deducted from the account
$response->getDeliveryStatus();   // string  Pending, Delivered, Undeliverable, Acknowledged, Expired, Accepted, Rejected, Unknown, Failed or DND
$response->getUniqueId();         // string  Carrier generated SMS ID
$response->getErrorCode();        // ?string Error code, if any
$response->getErrorDescription(); // ?string Error description, if any
$response->getSentDateTime();     // ?string Sent date in UTC, formatted as Y-m-d H:i:s
$response->getRemarks();          // ?string Remarks for the request
```

### Get balance

```php
use Kimulisiraj\SmsSpeedaMobile\SmsSpeedaMobile;

$client = new SmsSpeedaMobile(
    apiKey: 'your-username',
    apiSecret: 'your-password',
);

$client->getBalance(); // ['BalanceAmount' => 1000, 'CurrenceCode' => 'UGX']
```

## Error handling

| Exception | Thrown when |
| --- | --- |
| `Exceptions\InvalidNumberException` | The destination number is empty or not a valid `256` / `254` / `255` number |
| `Exceptions\InvalidMessageException` | The message is empty, longer than 160 characters, or a message id is missing |
| `Exceptions\SendException` | The send or status request failed, or the API returned an unusable payload |
| `Exceptions\SmsBroadcastException` | The balance request failed |
| `JsonException` | The API returned malformed JSON |

Credentials are stripped from exception messages, but they are still sent as query parameters, so avoid logging the full
request URI.

## Transport security

The Speeda Mobile API is only served over plain HTTP — `https://apidocs.speedamobile.com` does not route to the API. Your
`api_id` and `api_password` therefore travel unencrypted in the query string on every request. If the vendor enables TLS,
point the client at it without waiting for a release:

```php
use GuzzleHttp\Client as GuzzleClient;
use Kimulisiraj\SmsSpeedaMobile\Api\Client;

$client = new Client(
    new GuzzleClient(),
    'your-username',
    'your-password',
    baseUri: 'https://apidocs.speedamobile.com/api/',
);
```

## Testing

```bash
composer test
```

Code style and automated refactoring:

```bash
composer format
composer refactor
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Siraj Kimuli](https://github.com/kimulisiraj)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

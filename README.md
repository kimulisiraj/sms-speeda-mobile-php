# Send SMS using speed mobile api

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kimulisiraj/sms-speeda-mobile-php.svg?style=flat-square)](https://packagist.org/packages/kimulisiraj/sms-speeda-mobile-php)
[![Tests](https://github.com/kimulisiraj/sms-speeda-mobile-php/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/kimulisiraj/sms-speeda-mobile-php/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/kimulisiraj/sms-speeda-mobile-php.svg?style=flat-square)](https://packagist.org/packages/kimulisiraj/sms-speeda-mobile-php)

This is where your description should go. Try and limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require kimulisiraj/sms-speeda-mobile-php
```

## Usage

### Send single message
```php
$client = new \Kimulisiraj\SmsSpeedaMobile\SmsSpeedaMobile(
    apiKey:"your-username",
    apiSecret: "your-password",
);

$response = $client->send(
    to: 2567xxxxxxxx,
    mesage:'Hello, Kimulisiraj!'
)
return $response; 
```

### From the response you can access:
```php
$response->getStatus(); // OK | FAILED
$response->getMessage(); // Message sent successfully
$response->hasError(); // true | false
$response->getMessageId(); // I.E 12345
```

### Chaining 
```php
$client = Kimulisiraj\SmsSpeedaMobile::config([
    'apiKey' => 'your-api-key', //You can set and use env('SPEEDAMOBILE_SMS_API_ID')
    'apiPassword' => 'your-api-password', ////You can set and use env('SPEEDAMOBILE_SMS_API_PASSWORD')
]);

$response = $client->mesage('Hello, Kimulisiraj!')
        ->to('2567xxxxxxxx')
        ->send();

return $response 
```

### Get balance
```php
  $client = new \Kimulisiraj\SmsSpeedaMobile\SmsSpeedaMobile(
    apiKey:"your-username",
    apiSecret: "your-password",
);

$client->getBalance() // ['BalanceAmount' => 1000, 'CurrenceCode' => 'UGX'],
````
## Testing

```bash
composer test
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

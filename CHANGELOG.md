# Changelog

All notable changes to `sms-speeda-mobile-php` will be documented in this file.

## Unreleased

### Breaking

- Drops PHP 8.1. The package now requires PHP 8.2 or higher.
- `SmsSpeedaMobile::config()` returns a `SmsSpeedaMobile` instance instead of an `Api\Client`, so the documented
  `->message()->to()->send()` chain works.
- `Api\Client::send()` and `Api\Client::messageStatus()` now run request validation before calling the API, so an invalid
  number, an empty message, an over-length message or a missing message id throws instead of being sent upstream.
- `MessageStatusResponse` getters are typed. `getMessageLength()` and `getMessageParts()` return `int`,
  `getMessageCost()` returns `float`, and `getSentDateTime()` returns `?string`.
- `SendResponse::fromResponse()` throws a `SendException` when the payload is not a JSON object or is missing
  `remarks`, `message_id` or `status`, instead of emitting undefined key warnings.

### Fixed

- `MessageStatusResponse` used `Carbon\Carbon` without `nesbot/carbon` being required, so every `messageStatus()` call
  fatally errored unless the host application happened to ship Carbon. Replaced with `DateTimeImmutable`.
- `SmsSpeedaMobile::send()` and `messageStatus()` discarded state set through `to()`, `message()` and `messageId()`,
  sending empty values instead.
- `messageStatus()` did not send `api_id` / `api_password`.
- Only `RequestException` was caught, so connection failures and timeouts escaped as raw Guzzle exceptions.
- API credentials were interpolated into thrown exception messages. `api_password` is now redacted.
- Message length was measured with `strlen()`, rejecting valid multibyte messages of 160 characters or fewer.

### Changed

- The API base URI is injectable via the `Api\Client` constructor.
- Added Rector, updated PHP CS Fixer configuration, and migrated `phpunit.xml.dist` to the PHPUnit 11/12 schema.
- CI now runs the test suite against PHP 8.2, 8.3, 8.4 and 8.5.

## php 8.1 support - 2022-08-03

Official release
Drops php8.0

## 0.5.0 - 2022-02-10

### Initial release.

**Functionality**

- Send sms
- Get account balance

## 0.5.0 - 2022-02-10

- initial release
- [x] send sms
- [x] get balance

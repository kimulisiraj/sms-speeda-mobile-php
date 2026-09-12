# Changelog

All notable changes to `sms-speeda-mobile-php` will be documented in this file.

## 1.1.0 - 2026-09-12

### What's Changed

* Bump dependabot/fetch-metadata from 1.3.3 to 1.3.4 by @dependabot[bot] in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/9
* Bump dependabot/fetch-metadata from 1.3.4 to 1.3.5 by @dependabot[bot] in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/10
* Bump dependabot/fetch-metadata from 1.3.5 to 1.3.6 by @dependabot[bot] in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/11
* Bump dependabot/fetch-metadata from 1.3.6 to 1.4.0 by @dependabot[bot] in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/12
* Bump dependabot/fetch-metadata from 1.4.0 to 1.5.1 by @dependabot[bot] in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/13
* Bump dependabot/fetch-metadata from 1.5.1 to 1.6.0 by @dependabot[bot] in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/14
* Fixed and improved documentation in README.md by @macdanson in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/17
* Message Status Fetching by @macdanson in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/18
* Bump actions/checkout from 3 to 4 by @dependabot[bot] in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/15
* Bump stefanzweifel/git-auto-commit-action from 4 to 5 by @dependabot[bot] in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/16
* Bump stefanzweifel/git-auto-commit-action from 5 to 6 by @dependabot[bot] in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/19
* Bump actions/checkout from 4 to 5 by @dependabot[bot] in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/21
* Bump dependabot/fetch-metadata from 1.6.0 to 2.4.0 by @dependabot[bot] in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/20
* Bump stefanzweifel/git-auto-commit-action from 6 to 7 by @dependabot[bot] in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/22
* Bump dependabot/fetch-metadata from 2.4.0 to 2.5.0 by @dependabot[bot] in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/24
* Bump actions/checkout from 5 to 6 by @dependabot[bot] in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/23
* Bump dependabot/fetch-metadata from 2.5.0 to 3.1.0 by @dependabot[bot] in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/26
* Bump actions/checkout from 6 to 7 by @dependabot[bot] in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/27
* Chore/drop php 8.1 modernize tooling by @kimulisiraj in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/28

### New Contributors

* @macdanson made their first contribution in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/17
* @kimulisiraj made their first contribution in https://github.com/kimulisiraj/sms-speeda-mobile-php/pull/28

**Full Changelog**: https://github.com/kimulisiraj/sms-speeda-mobile-php/compare/1.0...1.1.0

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

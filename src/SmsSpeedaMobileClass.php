<?php

namespace Kimulisiraj\SmsSpeedaMobile;

class SmsSpeedaMobileClass
{
    public const API_URL = 'http://apidocs.speedamobile.com/api/SendSMS';

    public static function conf(string $apiKey, string $apiSecret): self
    {
        return new static($apiKey, $apiSecret);
    }

    public function __construct(
        protected string $apiKey,
        protected string $apiSecret,
    ) {
    }
}

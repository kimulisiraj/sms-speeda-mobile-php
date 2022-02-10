<?php

namespace Kimulisiraj\SmsSpeedaMobile;

use GuzzleHttp\RequestOptions;
use Kimulisiraj\SmsSpeedaMobile\Api\Client;
use Kimulisiraj\SmsSpeedaMobile\Api\SendResponse;

class SmsSpeedaMobile
{
    protected string $to = '';
    protected string $message = '';
    protected Client $client;

    public static function config(string $apiKey, string $apiPassword): Client
    {
        return new Client(
            new \GuzzleHttp\Client([
                RequestOptions::TIMEOUT => 5,
                RequestOptions::CONNECT_TIMEOUT => 5,
            ]),
            $apiKey,
            $apiPassword,
        );
    }

    public function __construct(
        protected string $apiKey,
        protected string $apiSecret,
    ) {
        $this->client = new Client(
            new \GuzzleHttp\Client([
                RequestOptions::TIMEOUT => 5,
                RequestOptions::CONNECT_TIMEOUT => 5,
            ]),
            $this->apiKey,
            $this->apiSecret,
        );
    }

    /**
     * @throws Exceptions\SendException
     */
    public function send(string $to = '', string $message = ''): SendResponse
    {
        $this->to = $to;
        $this->message = $message;

        return $this->client->send($to, $this->message);
    }

    public function to(string $to): self
    {
        $this->to = $to;

        return $this;
    }

    public function getBalance(): array
    {
        return $this->client->getBalance();
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}

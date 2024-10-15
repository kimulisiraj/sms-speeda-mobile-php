<?php

namespace Kimulisiraj\SmsSpeedaMobile;

use GuzzleHttp\RequestOptions;
use JsonException;
use Kimulisiraj\SmsSpeedaMobile\Api\Client;
use Kimulisiraj\SmsSpeedaMobile\Api\MessageStatusResponse;
use Kimulisiraj\SmsSpeedaMobile\Api\SendResponse;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\SendException;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\SmsBroadcastException;

class SmsSpeedaMobile
{
    protected string $to = '';
    protected string $message = '';
    protected string $messageId = '';
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
     * @throws Exceptions\SendException|JsonException
     */
    public function send(string $to = '', string $message = ''): SendResponse
    {
        $this->to = $to;
        $this->message = $message;

        return $this->client->send($to, $this->message);
    }

    /**
     * @throws SendException|JsonException
     */
    public function messageStatus(string $messageId = ''): MessageStatusResponse
    {
        $this->messageId = $messageId;

        return $this->client->messageStatus($messageId);
    }

    public function to(string $to): self
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @throws SmsBroadcastException
     * @throws JsonException
     */
    public function getBalance(): array
    {
        return $this->client->getBalance();
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function messageId(string $messageId): self
    {
        $this->messageId = $messageId;

        return $this;
    }
}

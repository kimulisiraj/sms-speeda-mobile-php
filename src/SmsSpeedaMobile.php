<?php

declare(strict_types=1);

namespace Kimulisiraj\SmsSpeedaMobile;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use JsonException;
use Kimulisiraj\SmsSpeedaMobile\Api\Client;
use Kimulisiraj\SmsSpeedaMobile\Api\MessageStatusResponse;
use Kimulisiraj\SmsSpeedaMobile\Api\SendResponse;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\InvalidMessageException;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\InvalidNumberException;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\SendException;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\SmsBroadcastException;

class SmsSpeedaMobile
{
    public const REQUEST_TIMEOUT = 5;

    protected string $to = '';

    protected string $message = '';

    protected string $messageId = '';

    protected Client $client;

    public function __construct(
        protected string $apiKey,
        protected string $apiSecret,
        ?Client $client = null,
    ) {
        $this->client = $client ?? $this->makeClient($apiKey, $apiSecret);
    }

    public static function config(string $apiKey, string $apiPassword): self
    {
        return new self($apiKey, $apiPassword);
    }

    /**
     * @throws SendException
     * @throws InvalidMessageException
     * @throws InvalidNumberException
     * @throws JsonException
     */
    public function send(string $to = '', string $message = ''): SendResponse
    {
        if ($to !== '') {
            $this->to = $to;
        }

        if ($message !== '') {
            $this->message = $message;
        }

        return $this->client->send($this->to, $this->message);
    }

    /**
     * @throws SendException
     * @throws InvalidMessageException
     * @throws JsonException
     */
    public function messageStatus(string $messageId = ''): MessageStatusResponse
    {
        if ($messageId !== '') {
            $this->messageId = $messageId;
        }

        return $this->client->messageStatus($this->messageId);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws SmsBroadcastException
     * @throws JsonException
     */
    public function getBalance(): array
    {
        return $this->client->getBalance();
    }

    public function to(string $to): self
    {
        $this->to = $to;

        return $this;
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

    private function makeClient(string $apiKey, string $apiPassword): Client
    {
        return new Client(
            new GuzzleClient([
                RequestOptions::TIMEOUT => self::REQUEST_TIMEOUT,
                RequestOptions::CONNECT_TIMEOUT => self::REQUEST_TIMEOUT,
            ]),
            $apiKey,
            $apiPassword,
        );
    }
}

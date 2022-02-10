<?php

namespace Kimulisiraj\SmsSpeedaMobile\Api;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\SendException;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\SmsBroadcastException;

class Client
{
    public const  API_ENDPOINT = 'http://apidocs.speedamobile.com/api/';
    protected const encoding = "T";
    public const ACTION_BALANCE = 'CheckBalance';
    public const ACTION_SEND = 'SendSMS';

    public function __construct(
        private \GuzzleHttp\Client $client,
        private string             $apiKey,
        private string             $apiPassword,
    ) {
    }

    public function send(string $to, string $message, ?string $senderId = 'BULKSMS')
    {
        $sendRequest = new SendRequest($to, $message, $senderId);

        try {
            $response = $this->client->get(self::API_ENDPOINT . self::ACTION_SEND, [
                RequestOptions::QUERY => [
                    ...$sendRequest->toRequest(),
                    "api_id" => $this->apiKey,
                    "api_password" => $this->apiPassword,
                ],

            ]);
        } catch (RequestException $exception) {
            throw new SendException(sprintf('Failed to send SMS: %s', $exception));
        }

        return SendResponse::fromResponse((string)$response->getBody());
    }

    /**
     * @throws SmsBroadcastException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getBalance(): array
    {
        try {
            $response = $this->client->get(self::API_ENDPOINT . self::ACTION_BALANCE, [
                RequestOptions::QUERY => [
                    'api_id' => $this->apiKey,
                    'api_password' => $this->apiPassword,
                ],
            ]);
        } catch (RequestException $exception) {
            throw new SmsBroadcastException(sprintf('Failed to fetch balance: %s', $exception->getMessage()));
        }

        return $this->parseBalanceResponse((string)$response->getBody());
    }

    private function parseBalanceResponse(string $content): array
    {
        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }
}

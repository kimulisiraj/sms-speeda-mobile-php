<?php

namespace Kimulisiraj\SmsSpeedaMobile\Api;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\RequestOptions;
use JsonException;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\SendException;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\SmsBroadcastException;

class Client
{
    public const  API_ENDPOINT = 'http://apidocs.speedamobile.com/api/';
    protected const encoding = "T";
    public const ACTION_BALANCE = 'CheckBalance';
    public const ACTION_SEND = 'SendSMS';
    public const ACTION_MESSAGE_STATUS = 'GetDeliveryStatus';

    public function __construct(
        private \GuzzleHttp\Client $client,
        private string             $apiKey,
        private string             $apiPassword,
    ) {}

    /**
     * @throws SendException
     * @throws JsonException
     */
    public function send(string $to, string $message, ?string $senderId = 'BULKSMS'): SendResponse
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
     * @throws SendException
     * @throws JsonException
     */
    public function messageStatus(string $messageId): MessageStatusResponse
    {
        $sendRequest = new MessageStatusRequest($messageId);

        try {
            $response = $this->client->get(self::API_ENDPOINT . self::ACTION_MESSAGE_STATUS,[
                RequestOptions::QUERY => [
                    ...$sendRequest->toRequest(),
                ],

            ]);
        } catch (RequestException $exception) {
            throw new SendException(sprintf('Failed to get SMS status: %s', $exception));
        }

        return MessageStatusResponse::fromStatusResponse((string)$response->getBody());
    }

    /**
     * @throws SmsBroadcastException
     * @throws GuzzleException
     * @throws JsonException
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

    /**
     * @throws JsonException
     */
    private function parseBalanceResponse(string $content): array
    {
        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }
}

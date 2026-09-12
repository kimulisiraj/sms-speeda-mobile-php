<?php

declare(strict_types=1);

namespace Kimulisiraj\SmsSpeedaMobile\Api;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use JsonException;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\InvalidMessageException;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\InvalidNumberException;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\SendException;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\SmsBroadcastException;

class Client
{
    public const API_ENDPOINT = 'http://apidocs.speedamobile.com/api/';

    public const ACTION_BALANCE = 'CheckBalance';

    public const ACTION_SEND = 'SendSMS';

    public const ACTION_MESSAGE_STATUS = 'GetDeliveryStatus';

    public function __construct(
        private readonly GuzzleClient $client,
        private readonly string $apiKey,
        private readonly string $apiPassword,
        private readonly string $baseUri = self::API_ENDPOINT,
    ) {
    }

    /**
     * @throws SendException
     * @throws InvalidMessageException
     * @throws InvalidNumberException
     * @throws JsonException
     */
    public function send(string $to, string $message, string $senderId = SendRequest::DEFAULT_SENDER_ID): SendResponse
    {
        $sendRequest = new SendRequest($to, $message, $senderId);
        $sendRequest->validate();

        $body = $this->get(
            self::ACTION_SEND,
            [...$sendRequest->toRequest(), ...$this->credentials()],
            'Failed to send SMS',
            SendException::class,
        );

        return SendResponse::fromResponse($body);
    }

    /**
     * @throws SendException
     * @throws InvalidMessageException
     * @throws JsonException
     */
    public function messageStatus(string $messageId): MessageStatusResponse
    {
        $statusRequest = new MessageStatusRequest($messageId);
        $statusRequest->validate();

        $body = $this->get(
            self::ACTION_MESSAGE_STATUS,
            [...$statusRequest->toRequest(), ...$this->credentials()],
            'Failed to get SMS status',
            SendException::class,
        );

        return MessageStatusResponse::fromStatusResponse($body);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws SmsBroadcastException
     * @throws JsonException
     */
    public function getBalance(): array
    {
        $body = $this->get(
            self::ACTION_BALANCE,
            $this->credentials(),
            'Failed to fetch balance',
            SmsBroadcastException::class,
        );

        $balance = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($balance)) {
            throw new SmsBroadcastException('Failed to fetch balance: response is not a JSON object');
        }

        return $balance;
    }

    /**
     * @param array<string, mixed> $query
     * @param class-string<SendException|SmsBroadcastException> $exceptionClass
     *
     * @throws SendException
     * @throws SmsBroadcastException
     */
    private function get(string $action, array $query, string $failureMessage, string $exceptionClass): string
    {
        try {
            $response = $this->client->get($this->baseUri . $action, [
                RequestOptions::QUERY => $query,
            ]);
        } catch (BadResponseException $exception) {
            throw new $exceptionClass(
                sprintf(
                    '%s: %s',
                    $failureMessage,
                    $this->redact((string) $exception->getResponse()->getBody()),
                ),
                (int) $exception->getCode(),
                $exception,
            );
        } catch (GuzzleException $exception) {
            throw new $exceptionClass(
                sprintf('%s: %s', $failureMessage, $this->redact($exception->getMessage())),
                (int) $exception->getCode(),
                $exception,
            );
        }

        return (string) $response->getBody();
    }

    /**
     * @return array{api_id: string, api_password: string}
     */
    private function credentials(): array
    {
        return [
            'api_id' => $this->apiKey,
            'api_password' => $this->apiPassword,
        ];
    }

    private function redact(string $message): string
    {
        return preg_replace('/(api_password=)[^&\s\'"]*/i', '$1***', $message) ?? $message;
    }
}

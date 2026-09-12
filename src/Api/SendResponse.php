<?php

declare(strict_types=1);

namespace Kimulisiraj\SmsSpeedaMobile\Api;

use JsonException;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\SendException;

class SendResponse
{
    public const FAILED = 'F';

    public const SUCCESS = 'S';

    public function __construct(
        private readonly string $message,
        private readonly string $code,
        private readonly string $status,
    ) {
    }

    public function hasError(): bool
    {
        return $this->status === self::FAILED;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getStatus(): string
    {
        return $this->hasError() ? 'FAILED' : 'OK';
    }

    public function getError(): ?string
    {
        return $this->hasError() ? $this->message : null;
    }

    public function getMessageId(): string
    {
        return $this->code;
    }

    /**
     * @throws SendException
     * @throws JsonException
     */
    public static function fromResponse(string $response): self
    {
        $decoded = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($decoded)) {
            throw new SendException('Send response is not a JSON object');
        }

        foreach (['remarks', 'message_id', 'status'] as $requiredKey) {
            if (! array_key_exists($requiredKey, $decoded)) {
                throw new SendException(sprintf('Send response is missing the `%s` key', $requiredKey));
            }
        }

        return new self(
            message: (string) $decoded['remarks'],
            code: (string) $decoded['message_id'],
            status: (string) $decoded['status'],
        );
    }
}

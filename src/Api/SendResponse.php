<?php

namespace Kimulisiraj\SmsSpeedaMobile\Api;

class SendResponse
{
    public const FAILED = 'F';
    public const SUCCESS = 'S';

    public function __construct(
        private string $message,
        private string  $code,
        private string  $status,
    ) {
    }

    public function hasError(): bool
    {
        return  $this->status === self::FAILED;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @return string
     */
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
     * @throws \JsonException
     */
    public static function fromResponse(string $response): self
    {
        $res = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        return new SendResponse(
            message: $res['remarks'],
            code: $res['message_id'],
            status: $res['status']
        );
    }
}

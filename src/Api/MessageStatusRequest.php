<?php

namespace Kimulisiraj\SmsSpeedaMobile\Api;

use Kimulisiraj\SmsSpeedaMobile\Exceptions\InvalidMessageException;

class MessageStatusRequest
{
    public function __construct(
        private string  $messageId,
    ) {
    }

    /**
     * @throws InvalidMessageException
     */
    public function validate(): void
    {
        if (empty($this->messageId)) {
            throw new InvalidMessageException('No `messageId` provided');
        }
    }

    /**
     * @return array{message_id: string}
     */
    public function toRequest(): array
    {
        return [
            "message_id" => $this->messageId,
        ];
    }
}

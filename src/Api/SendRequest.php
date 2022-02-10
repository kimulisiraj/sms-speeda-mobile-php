<?php

namespace Kimulisiraj\SmsSpeedaMobile\Api;

use Kimulisiraj\SmsSpeedaMobile\Exceptions\InvalidMessageException;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\InvalidNumberException;

class SendRequest
{
    public const VALID_NUMBER_REGEX = '/^(?:256|254|255)[\d]{9}$/';

    public const MESSAGE_MAX_LENGTH_STANDARD = 160;

    public function __construct(
        private string  $to,
        private string  $message,
        private ?string $senderId = 'BULKSMS',
        private ?string $smsType = 'P',
        private ?string $templeteId = null
    ) {
    }

    /**
     * @throws InvalidMessageException
     * @throws InvalidNumberException
     */
    public function validate(): void
    {
        if (empty($this->to)) {
            throw new InvalidNumberException('No `to` number(s)');
        }

        if (! preg_match(self::VALID_NUMBER_REGEX, $this->to)) {
            throw new InvalidNumberException(sprintf('Message to number `%s` is invalid', $this->to));
        }

        if (empty($this->message)) {
            throw new InvalidMessageException('Message is empty');
        }

        $maxLength = self::MESSAGE_MAX_LENGTH_STANDARD;

        if (strlen($this->message) > $maxLength) {
            throw new InvalidMessageException(sprintf(
                'Message length `%s` of chars is over maximum length of `%s` chars',
                strlen($this->message),
                $maxLength
            ));
        }
    }

    public function toRequest(): array
    {
        return [
            'encoding' => 'T',
            "textmessage" => $this->message,
            "phonenumber" => $this->to,
            "sender_id" => $this->senderId,
            "sms_type" => $this->smsType,
            "templateid" => $this->templeteId,
        ];
    }
}

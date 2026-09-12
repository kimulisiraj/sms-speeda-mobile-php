<?php

declare(strict_types=1);

namespace Kimulisiraj\SmsSpeedaMobile\Api;

use Kimulisiraj\SmsSpeedaMobile\Exceptions\InvalidMessageException;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\InvalidNumberException;

class SendRequest
{
    public const VALID_NUMBER_REGEX = '/^(?:256|254|255)\d{9}$/';

    public const MESSAGE_MAX_LENGTH_STANDARD = 160;

    public const DEFAULT_SENDER_ID = 'BULKSMS';

    public const ENCODING_TEXT = 'T';

    public const SMS_TYPE_PROMOTIONAL = 'P';

    public function __construct(
        private readonly string $to,
        private readonly string $message,
        private readonly string $senderId = self::DEFAULT_SENDER_ID,
        private readonly string $smsType = self::SMS_TYPE_PROMOTIONAL,
        private readonly ?string $templateId = null,
    ) {
    }

    /**
     * @throws InvalidMessageException
     * @throws InvalidNumberException
     */
    public function validate(): void
    {
        if ($this->to === '') {
            throw new InvalidNumberException('No `to` number(s)');
        }

        if (! preg_match(self::VALID_NUMBER_REGEX, $this->to)) {
            throw new InvalidNumberException(sprintf('Message to number `%s` is invalid', $this->to));
        }

        if ($this->message === '') {
            throw new InvalidMessageException('Message is empty');
        }

        $messageLength = mb_strlen($this->message);

        if ($messageLength > self::MESSAGE_MAX_LENGTH_STANDARD) {
            throw new InvalidMessageException(sprintf(
                'Message length `%s` of chars is over maximum length of `%s` chars',
                $messageLength,
                self::MESSAGE_MAX_LENGTH_STANDARD,
            ));
        }
    }

    /**
     * @return array{
     *     encoding: string,
     *     textmessage: string,
     *     phonenumber: string,
     *     sender_id: string,
     *     sms_type: string,
     *     templateid: ?string
     * }
     */
    public function toRequest(): array
    {
        return [
            'encoding' => self::ENCODING_TEXT,
            'textmessage' => $this->message,
            'phonenumber' => $this->to,
            'sender_id' => $this->senderId,
            'sms_type' => $this->smsType,
            'templateid' => $this->templateId,
        ];
    }
}

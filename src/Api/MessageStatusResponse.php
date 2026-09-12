<?php

declare(strict_types=1);

namespace Kimulisiraj\SmsSpeedaMobile\Api;

use DateTimeImmutable;
use Exception;
use JsonException;
use Kimulisiraj\SmsSpeedaMobile\Exceptions\SendException;

class MessageStatusResponse
{
    public function __construct(
        private readonly string $messageId,
        private readonly string $phoneNumber,
        private readonly string $messageBody,
        private readonly string $messageType,
        private readonly int $messageLength,
        private readonly int $messageParts,
        private readonly float $messageCost,
        private readonly string $deliveryStatus,
        private readonly string $uniqueId,
        private readonly ?string $errorCode,
        private readonly ?string $errorDescription,
        private readonly ?string $sentDateTime,
        private readonly ?string $remarks,
    ) {
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getMessageBody(): string
    {
        return $this->messageBody;
    }

    public function getMessageType(): string
    {
        return $this->messageType;
    }

    public function getMessageLength(): int
    {
        return $this->messageLength;
    }

    public function getMessageParts(): int
    {
        return $this->messageParts;
    }

    public function getMessageCost(): float
    {
        return $this->messageCost;
    }

    public function getDeliveryStatus(): string
    {
        return $this->deliveryStatus;
    }

    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getErrorDescription(): ?string
    {
        return $this->errorDescription;
    }

    public function getSentDateTime(): ?string
    {
        return $this->sentDateTime;
    }

    public function getRemarks(): ?string
    {
        return $this->remarks;
    }

    /**
     * @throws SendException
     * @throws JsonException
     */
    public static function fromStatusResponse(string $response): self
    {
        $decoded = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($decoded)) {
            throw new SendException('Message status response is not a JSON object');
        }

        return new self(
            messageId: (string) ($decoded['message_id'] ?? ''),
            phoneNumber: (string) ($decoded['PhoneNumber'] ?? ''),
            messageBody: (string) ($decoded['SMSMessage'] ?? ''),
            messageType: (string) ($decoded['MessageType'] ?? ''),
            messageLength: (int) ($decoded['MessageLength'] ?? 0),
            messageParts: (int) ($decoded['MessageParts'] ?? 0),
            messageCost: (float) ($decoded['ClientCost'] ?? 0),
            deliveryStatus: (string) ($decoded['DLRStatus'] ?? ''),
            uniqueId: (string) ($decoded['SMSID'] ?? ''),
            errorCode: self::nullableString($decoded['ErrorCode'] ?? null),
            errorDescription: self::nullableString($decoded['ErrorDescription'] ?? null),
            sentDateTime: self::formatSentDate($decoded['SentDateUTC'] ?? null),
            remarks: self::nullableString($decoded['Remarks'] ?? null),
        );
    }

    private static function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string) $value;
    }

    private static function formatSentDate(mixed $sentDateUtc): ?string
    {
        if (! is_string($sentDateUtc)) {
            return null;
        }

        if (trim($sentDateUtc) === '') {
            return null;
        }

        try {
            return (new DateTimeImmutable($sentDateUtc))->format('Y-m-d H:i:s');
        } catch (Exception) {
            return null;
        }
    }
}

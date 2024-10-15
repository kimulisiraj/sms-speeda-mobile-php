<?php

namespace Kimulisiraj\SmsSpeedaMobile\Api;

use Carbon\Carbon;
use JsonException;

class MessageStatusResponse
{
    public function __construct(
        private string  $messageId,
        private string  $phoneNumber,
        private string  $messageBody,
        private string  $MessageType,
        private string  $messageLength,
        private string  $messageParts,
        private string  $messageCost,
        private string  $deliveryStatus,
        private string  $uniqueId,
        private int  $errorCode,
        private string  $errorDescription,
        private string  $sentDateTime,
        private string  $remarks,
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
        return $this->MessageType;
    }

    public function getMessageLength(): string
    {
        return $this->messageLength;
    }

    public function getMessageParts(): string
    {
        return $this->messageParts;
    }

    public function getMessageCost(): string
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

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    public function getErrorDescription(): string
    {
        return $this->errorDescription;
    }

    public function getSentDateTime(): string
    {
        return $this->sentDateTime;
    }

    public function getRemarks(): string
    {
        return $this->remarks;
    }

    /**
     * @throws JsonException
     */
    public static function fromStatusResponse(string $response): self
    {
        $res = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        return new MessageStatusResponse(
            messageId: $res['message_id'],
            phoneNumber: $res['PhoneNumber'],
            messageBody: $res['SMSMessage'],
            MessageType: $res['MessageType'],
            messageLength: $res['MessageLength'],
            messageParts: $res['MessageParts'],
            messageCost: $res['ClientCost'],
            deliveryStatus: $res['DLRStatus'],
            uniqueId: $res['SMSID'],
            errorCode: $res['ErrorCode'],
            errorDescription: $res['ErrorDescription'],
            sentDateTime: Carbon::parse($res['SentDateUTC'])->format('Y-m-d H:i:s'),
            remarks: $res['Remarks']
        );
    }
}

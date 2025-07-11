<?php

namespace Kimulisiraj\SmsSpeedaMobile\Api;

use Carbon\Carbon;
use JsonException;

class MessageStatusResponse
{
    public function __construct(
        private  $messageId,
        private  $phoneNumber,
        private  $messageBody,
        private  $MessageType,
        private  $messageLength,
        private  $messageParts,
        private  $messageCost,
        private  $deliveryStatus,
        private  $uniqueId,
        private  $errorCode,
        private  $errorDescription,
        private  $sentDateTime,
        private  $remarks,
    ) {
    }

    public function getMessageId()
    {
        return $this->messageId;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function getMessageBody()
    {
        return $this->messageBody;
    }

    public function getMessageType()
    {
        return $this->MessageType;
    }

    public function getMessageLength()
    {
        return $this->messageLength;
    }

    public function getMessageParts()
    {
        return $this->messageParts;
    }

    public function getMessageCost()
    {
        return $this->messageCost;
    }

    public function getDeliveryStatus()
    {
        return $this->deliveryStatus;
    }

    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function getErrorDescription()
    {
        return $this->errorDescription;
    }

    public function getSentDateTime()
    {
        return $this->sentDateTime;
    }

    public function getRemarks()
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

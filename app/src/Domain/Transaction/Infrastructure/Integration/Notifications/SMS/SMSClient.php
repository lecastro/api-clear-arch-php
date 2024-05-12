<?php

namespace Domain\Transaction\Infrastructure\Integration\Notifications\SMS;

use Domain\Transaction\Infrastructure\Integration\Notifications\AdapterSMSNotification;

class SMSClient implements AdapterSMSNotification
{
    public function sendSMSNotification(): bool
    {
        return true;
    }
}

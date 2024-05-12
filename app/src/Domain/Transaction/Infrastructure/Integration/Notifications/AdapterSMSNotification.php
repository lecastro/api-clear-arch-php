<?php

namespace Domain\Transaction\Infrastructure\Integration\Notifications;

interface AdapterSMSNotification
{
    public function sendSMSNotification(): bool;
}

<?php

namespace Domain\Transaction\Infrastructure\Integration\Notifications;

interface AdapterEmailNotification
{
    public function sendEmailNotification(): bool;
}

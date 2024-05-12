<?php

namespace Domain\Transaction\Infrastructure\Integration\Notifications\Mail;

use Domain\Transaction\Infrastructure\Integration\Notifications\AdapterEmailNotification;

class MailClient implements AdapterEmailNotification
{
    public function sendEmailNotification(): bool
    {
        return true;
    }
}

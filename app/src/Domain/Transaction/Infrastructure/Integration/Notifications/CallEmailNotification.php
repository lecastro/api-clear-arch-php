<?php

namespace Domain\Transaction\Infrastructure\Integration\Notifications;

class CallEmailNotification
{
    public function __construct(protected AdapterEmailNotification $notification)
    {
    }

    public function getAdapter(): AdapterEmailNotification
    {
        return $this->notification;
    }
}

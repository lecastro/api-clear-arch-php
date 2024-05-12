<?php

use Domain\Transaction\Infrastructure\Integration\Notifications\SMS\SMSClientMock;
use Domain\Transaction\Infrastructure\Integration\Notifications\CallSMSNotification;

test('should make a call SMS notification', function () {
    $this->notification = new CallSMSNotification(new SMSClientMock());

    expect($this->notification->getAdapter())->not->toBeNull();
    expect($this->notification->getAdapter())->toBeInstanceOf(SMSClientMock::class);
    expect($this->notification->getAdapter()->sendSMSNotification())->toBeTrue();
});

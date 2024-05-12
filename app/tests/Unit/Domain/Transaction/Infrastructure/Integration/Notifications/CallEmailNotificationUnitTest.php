<?php

use Domain\Transaction\Infrastructure\Integration\Notifications\Mail\MailClientMock;
use Domain\Transaction\Infrastructure\Integration\Notifications\CallEmailNotification;
test('should make a call email notification', function () {
    $this->notification = new CallEmailNotification(new MailClientMock());

    expect($this->notification->getAdapter())->not->toBeNull();
    expect($this->notification->getAdapter())->toBeInstanceOf(MailClientMock::class);
    expect($this->notification->getAdapter()->sendEmailNotification())->toBeTrue();
});

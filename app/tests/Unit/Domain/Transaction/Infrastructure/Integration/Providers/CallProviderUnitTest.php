<?php

use Domain\Transaction\Infrastructure\Integration\Providers\CallProvider;
use Domain\Transaction\Infrastructure\Integration\Providers\Picpay\PicpayClientMock;

test('should make a call provider', function () {
    $this->provider = new CallProvider(new PicpayClientMock());

    expect($this->provider->getAdapter())->not->toBeNull();
    expect($this->provider->getAdapter())->toBeInstanceOf(PicpayClientMock::class);
    expect($this->provider->getAdapter()->authorizeTransaction())->toBeTrue();
});

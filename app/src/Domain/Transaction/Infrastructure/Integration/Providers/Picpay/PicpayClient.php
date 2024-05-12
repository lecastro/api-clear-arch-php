<?php

namespace Domain\Transaction\Domain\Infrastructure\Integration\Providers\Picpay;

use Domain\Transaction\Infrastructure\Integration\Providers\AdapterProviderInterface;

class PicpayClient implements AdapterProviderInterface
{
    public function authorizeTransaction(): bool
    {
        return true;
    }
}

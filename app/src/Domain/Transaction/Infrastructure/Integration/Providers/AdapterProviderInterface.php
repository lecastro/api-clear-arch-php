<?php

namespace Domain\Transaction\Infrastructure\Integration\Providers;

interface AdapterProviderInterface
{
    public function authorizeTransaction(): bool;
}

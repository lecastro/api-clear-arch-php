<?php

namespace Core\Wallet\Domain\Entities;

use DateTime;
use Core\SeedWork\Domain\ValueObjects\Uuid;
use Core\SeedWork\Domain\Enums\TypeUserEnum;
use Core\Wallet\Domain\validator\WalletValidation;
use Core\SeedWork\Domain\Traits\MethodsMagicsTrait;

class Wallet
{
    use MethodsMagicsTrait;

    function __construct(
        protected null|Uuid $id = null,
        protected TypeUserEnum $userType,
        protected Uuid $userId,
        protected float $balance = 0,
        protected ?DateTime $createdAt = null,
    ) {
        $this->id = $this->id ?? Uuid::random();
        $this->createdAt = $this->createdAt ?? new DateTime();

        $this->validate();
    }

    public function hasBalance(float $amount): bool
    {
        return $this->balance < $amount;
    }

    private function validate(): void
    {
        WalletValidation::hasBalance($this->balance);
    }
}

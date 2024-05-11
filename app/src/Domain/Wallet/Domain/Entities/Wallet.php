<?php

namespace Domain\Wallet\Domain\Entities;

use DateTime;
use Domain\Shared\ValueObjects\Uuid;
use Domain\Shared\Enums\TypeUserEnum;
use Domain\Wallet\Domain\validator\WalletValidation;
use Domain\Shared\Traits\MethodsMagicsTrait;

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

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function deposit(float $amount): void
    {
        WalletValidation::validateValueNegative($amount);

        $this->balance += $amount;
    }

    public function withdrawal(float $amount): void
    {
        if ($this->hasBalance($amount)) {
            WalletValidation::validateBalance($amount);
        }

        $this->balance -= $amount;
    }

    private function validate(): void
    {
        WalletValidation::validateValueNegative($this->balance);
    }

    public function hasRetailer(): bool
    {
        return $this->userType == TypeUserEnum::RETAILER;
    }
}

<?php

namespace Domain\Transaction\Domain\Entities;

use DateTime;
use Domain\Shared\ValueObjects\Uuid;
use Domain\Shared\Traits\MethodsMagicsTrait;
use Domain\Transaction\Domain\validator\TransactionValidation;
use Domain\Transaction\Domain\Enums\TransactionStatusEnum;

class Transaction
{
    use MethodsMagicsTrait;

    public function __construct(
        protected null|Uuid $id = null,
        protected Uuid $payerId,
        protected Uuid $payeeId,
        protected float $amount,
        protected TransactionStatusEnum $status,
        protected ?DateTime $createdAt = null,
    ) {
        $this->id = $this->id ?? Uuid::random();
        $this->createdAt = $this->createdAt ?? new DateTime();

        $this->validate();
    }

    private function validate(): void
    {
        TransactionValidation::validateValueNegative($this->amount);
        TransactionValidation::validateType($this->status);
    }

    public function updateStatus(TransactionStatusEnum $status): void
    {
        $this->status = $status;
    }
}

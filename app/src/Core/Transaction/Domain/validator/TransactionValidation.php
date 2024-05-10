<?php

namespace Core\Transaction\Domain\validator;

use Core\Transaction\Domain\Enums\TransactionStatusEnum;
use Core\Transaction\Domain\validator\Exceptions\NegativeBalanceException;
use Core\Transaction\Domain\validator\Exceptions\EntityValidationException;

class TransactionValidation
{
    public static function validateValueNegative(float $value): void
    {
        if ($value < 0) {
            throw new NegativeBalanceException("Negative balance is not allowed {$value}");
        }
    }

    public static function validateType(TransactionStatusEnum $status = null, string $customMessage = null): void
    {
        if ($status->value == TransactionStatusEnum::DEFAULT->value) {
            throw new EntityValidationException("The type provided is not valid {$status->value}");
        }
    }
}

<?php

namespace Domain\Transaction\Domain\validator;

use Domain\Transaction\Domain\Enums\TransactionStatusEnum;
use Domain\Transaction\Domain\validator\Exceptions\TransactionException;
use Domain\Transaction\Domain\validator\Exceptions\NegativeBalanceException;
use Domain\Transaction\Domain\validator\Exceptions\EntityValidationException;
use Domain\Transaction\Domain\validator\Exceptions\InsufficientBalanceException;
use Domain\Transaction\Domain\validator\Exceptions\RetailerNotAllowedToPayException;

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

    public static function retailerNotAllowedToPay(string $message = null): void
    {
        throw new RetailerNotAllowedToPayException("Retailers are not allowed to pay anyone. {$message}");
    }

    public static function noMoneyOnWallet(): void
    {
        throw new InsufficientBalanceException("No money on wallet.");
    }

    public static function notAuthorized(): void
    {
        throw new TransactionException('not authorized');
    }

    public static function transactionMessageNotSent(): void
    {
        throw new TransactionException('nessage not sent');
    }
}

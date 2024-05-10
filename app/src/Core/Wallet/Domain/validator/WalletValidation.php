<?php

namespace Core\Wallet\Domain\validator;

use Core\User\Domain\validator\Exceptions\NegativeBalanceException;

class WalletValidation
{
    public static function hasBalance(float $value): void
    {
        if ($value < 0) {
            throw new NegativeBalanceException("Negative balance is not allowed {$value}");
        }
    }
}

<?php

namespace Usecase\Transaction\Create\DTO;

class InputTransactionDTO
{
    public function __construct(
        public readonly float $value,
        public readonly string $payer,
        public readonly string $payee
    ) {
    }
}

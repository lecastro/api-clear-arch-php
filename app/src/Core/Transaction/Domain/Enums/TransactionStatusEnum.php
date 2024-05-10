<?php

namespace Core\Transaction\Domain\Enums;

enum TransactionStatusEnum: string
{
    case CREATED    = 'created';
    case WITHDRAWED = 'withdrawed';
    case DEBITED    = 'debited';
    case COMPLETED  = 'completed';
    case DEFAULT    = 'default';
}

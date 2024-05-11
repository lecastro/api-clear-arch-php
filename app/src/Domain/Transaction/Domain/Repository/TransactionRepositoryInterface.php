<?php

namespace Domain\Transaction\Domain\Repository;

use Domain\Transaction\Domain\Entities\Transaction;

interface TransactionRepositoryInterface
{
    public function create(Transaction $transaction): void;
    public function findByIdTransaction(string $id): ?Transaction;
}

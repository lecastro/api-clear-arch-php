<?php

namespace Core\Transaction\Domain\Repository;

use Core\Transaction\Domain\Entities\Transaction;

interface TransactionRepositoryInterface
{
    public function create(Transaction $transaction): void;
    public function findByIdTransaction(string $id): ?Transaction;
}

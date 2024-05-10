<?php

namespace Core\Wallet\Domain\Repository;

use Core\User\Domain\Entities\User;
use Core\Wallet\Domain\Entities\Wallet;

interface WalletRepositoryInterface
{
    public function create(User $user): Wallet;
    public function findByWalletByUserId(string $userId): ?Wallet;
    public function findById(string $walletId): ?Wallet;
    public function deposit(string $walletId, int $amount): bool;
    public function withdrawal(string $walletId, int $amount): bool;
}
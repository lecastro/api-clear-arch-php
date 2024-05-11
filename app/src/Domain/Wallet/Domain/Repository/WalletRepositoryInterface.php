<?php

namespace Domain\Wallet\Domain\Repository;

use Domain\User\Domain\Entities\User;
use Domain\Wallet\Domain\Entities\Wallet;

interface WalletRepositoryInterface
{
    public function create(User $user): Wallet;
    public function findByWalletByUserId(string $userId): ?Wallet;
    public function findById(string $walletId): ?Wallet;
    public function getWalletByPayerId(string $payerId): ?Wallet;
    public function getWalletByPayeeId(string $payeeId): ?Wallet;
    public function deposit(string $walletId, int $amount): bool;
    public function withdrawal(string $walletId, int $amount): bool;
}

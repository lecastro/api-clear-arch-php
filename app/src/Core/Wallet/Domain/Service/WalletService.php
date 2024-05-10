<?php

namespace Core\Wallet\Domain\Service;

use Core\User\Domain\Entities\User;
use Core\Wallet\Domain\Entities\Wallet;
use Core\Wallet\Domain\Repository\WalletRepositoryInterface;

class WalletService
{
    public function __construct(protected WalletRepositoryInterface $walletRepository)
    {
    }

    public function create(User $user): Wallet
    {
        $wallet = $this->walletRepository->findByWalletByUserId($user->id());

        if ($wallet !== null) {
            return $wallet;
        }

        return $this->walletRepository->create($user);
    }

    public function findById(string $walletId): ?Wallet
    {
        return $this->walletRepository->findById($walletId);
    }

    public function hasBalance(string $walletId, float $amount): bool
    {
        $wallet = $this->walletRepository->findById($walletId);

        if ($wallet === null) {
            return false;
        }

        return $wallet->hasBalance($amount);
    }

    public function getBalance(string $walletId): float
    {
        $wallet = $this->walletRepository->findById($walletId);

        if ($wallet === null) {
            return 0;
        }

        return $wallet->getBalance();
    }

    public function deposit(string $walletId, float $amount): void
    {
        try {
            $wallet = $this->walletRepository->findById($walletId);

            if ($wallet === null) {
                return;
            }

            $wallet->deposit($amount);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function withdrawal(string $walletId, float $amount): void
    {
        $wallet = $this->walletRepository->findById($walletId);

        if ($wallet === null) {
            return;
        }

        $wallet->withdrawal($amount);
    }
}
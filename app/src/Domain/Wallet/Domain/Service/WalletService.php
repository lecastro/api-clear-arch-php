<?php

namespace Domain\Wallet\Domain\Service;

use Domain\User\Domain\Entities\User;
use Domain\Wallet\Domain\Entities\Wallet;
use Domain\Wallet\Domain\Repository\WalletRepositoryInterface;

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

    public function getWalletByPayerId(string $payerId): ?Wallet
    {
        return $this->walletRepository->getWalletByPayerId($payerId);
    }

    public function getWalletByPayeeId(string $payeeId): ?Wallet
    {
        return $this->walletRepository->getWalletByPayeeId($payeeId);
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

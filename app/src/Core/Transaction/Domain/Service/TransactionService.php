<?php

namespace Core\Transaction\Domain\Service;

use Core\Wallet\Domain\Service\WalletService;
use Core\Transaction\Domain\Entities\Transaction;
use Core\Transaction\Domain\Enums\TransactionStatusEnum;
use Core\Transaction\Domain\validator\TransactionValidation;
use Core\Transaction\Domain\Repository\TransactionRepositoryInterface;

class TransactionService
{
    public function __construct(
        protected TransactionRepositoryInterface $repository,
        protected WalletService $walletService
    ) {
    }

    public function create(Transaction $transaction): void
    {
        try {
            $payerWallet = $this->walletService->getWalletByPayerId($transaction->payerId());
            $payeeWallet = $this->walletService->getWalletByPayeeId($transaction->payeeId());

            if ($payerWallet->hasRetailer()) {
                TransactionValidation::retailerNotAllowedToPay();
            }

            if ($payerWallet->hasBalance($transaction->amount())) {
                TransactionValidation::noMoneyOnWallet();
            }

            $payeeWallet->deposit($transaction->amount());
            $payerWallet->withdrawal($transaction->amount());

            $transaction->updateStatus(TransactionStatusEnum::COMPLETED);

            // authorizePayment
            // sendPaymentApproval
            $this->repository->create($transaction);
        } catch (\Throwable $th) {
            $this->cancelTransaction($transaction);
            throw $th;
        }
    }

    public function cancelTransaction(Transaction $transaction): void
    {
        $transaction->updateStatus(TransactionStatusEnum::CANCELED);
        $this->repository->create($transaction);
    }
}

<?php

namespace Domain\Transaction\Domain\Service;

use Domain\Wallet\Domain\Service\WalletService;
use Domain\Transaction\Domain\Entities\Transaction;
use Domain\Transaction\Domain\Enums\TransactionStatusEnum;
use Domain\Transaction\Domain\validator\TransactionValidation;
use Domain\Transaction\Domain\Repository\TransactionRepositoryInterface;

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

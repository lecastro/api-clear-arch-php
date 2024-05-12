<?php

namespace Domain\Transaction\Domain\Service;

use Domain\Wallet\Domain\Service\WalletService;
use Domain\Transaction\Domain\Entities\Transaction;
use Domain\Transaction\Domain\Enums\TransactionStatusEnum;
use Domain\Transaction\Domain\validator\TransactionValidation;
use Domain\Transaction\Domain\Repository\TransactionRepositoryInterface;
use Domain\Transaction\Infrastructure\Integration\Providers\AdapterProviderInterface;
use Domain\Transaction\Infrastructure\Integration\Notifications\AdapterSMSNotification;
use Domain\Transaction\Infrastructure\Integration\Notifications\AdapterEmailNotification;

class TransactionService
{
    public function __construct(
        protected TransactionRepositoryInterface $repository,
        protected WalletService $walletService,
        protected AdapterProviderInterface $provider,
        protected AdapterEmailNotification $mail,
        protected AdapterSMSNotification $sms
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

            $this->sendAuthorizeTransaction();
            $this->sendSMSNotification();

            $this->repository->create($transaction);
        } catch (\Throwable $th) {
            $this->cancelTransaction($transaction);
            throw $th;
        }
    }

    private function cancelTransaction(Transaction $transaction): void
    {
        $transaction->updateStatus(TransactionStatusEnum::CANCELED);
        $this->repository->create($transaction);
    }

    private function sendAuthorizeTransaction(): void
    {
        if (!$this->provider->authorizeTransaction()) {
            TransactionValidation::notAuthorized();
        }
    }

    private function sendSMSNotification()
    {
        if (!$this->mail->sendEmailNotification() || !$this->sms->sendSMSNotification()) {
            TransactionValidation::transactionMessageNotSent();
        }
    }
}

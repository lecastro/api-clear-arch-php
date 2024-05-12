<?php

use Faker\Factory;
use Mockery\MockInterface;
use Domain\Shared\Enums\TypeUserEnum;
use Domain\User\Domain\Entities\User;
use Domain\Shared\ValueObjects\Document;
use Domain\Wallet\Domain\Entities\Wallet;
use Domain\Wallet\Domain\Service\WalletService;
use Domain\Transaction\Domain\Entities\Transaction;
use Domain\Transaction\Domain\Service\TransactionService;
use Domain\Transaction\Domain\Enums\TransactionStatusEnum;
use Domain\Wallet\Domain\Repository\WalletRepositoryInterface;
use Domain\Transaction\Domain\Repository\TransactionRepositoryInterface;
use Domain\Transaction\Infrastructure\Integration\Providers\CallProvider;
use Domain\Transaction\Domain\validator\Exceptions\InsufficientBalanceException;
use Domain\Transaction\Domain\validator\Exceptions\RetailerNotAllowedToPayException;
use Domain\Transaction\Infrastructure\Integration\Notifications\CallSMSNotification;
use Domain\Transaction\Infrastructure\Integration\Providers\AdapterProviderInterface;
use Domain\Transaction\Infrastructure\Integration\Notifications\CallEmailNotification;
use Domain\Transaction\Infrastructure\Integration\Notifications\AdapterSMSNotification;
use Domain\Transaction\Infrastructure\Integration\Notifications\AdapterEmailNotification;

beforeEach(function () {
    $this->payer = new User(
        id: null,
        name: Factory::create()->name(),
        email: Factory::create()->email(),
        document: new Document('123.456.789-09'),
        password: '1234567',
        type: TypeUserEnum::CUSTOMER,
    );

    $this->payee = new User(
        id: null,
        name: Factory::create()->name(),
        email: Factory::create()->email(),
        document: new Document('123.456.789-09'),
        password: '1234567',
        type: TypeUserEnum::RETAILER,
    );

    $this->payerWallet = new Wallet(
        id: null,
        userType: $this->payer->type,
        userId: $this->payer->id,
        balance: 500.0
    );

    $this->payeeWallet = new Wallet(
        id: null,
        userType: $this->payee->type,
        userId: $this->payee->id,
        balance: 0.0
    );

    $this->transaction = new Transaction(
        id: null,
        payerId: $this->payer->id,
        payeeId: $this->payee->id,
        amount: 100.0,
        status: TransactionStatusEnum::CREATED
    );

    $this->transactionRepositoryMock = mock(TransactionRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('create')->with($this->transaction)->once();
        $mock->shouldReceive('findByIdTransaction')->with($this->transaction->id->get())->andReturn($this->transaction);
    });

    $this->walletRepositoryMock = mock(WalletRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('getWalletByPayerId')->with($this->transaction->payerId())->andReturn($this->payerWallet);
        $mock->shouldReceive('getWalletByPayeeId')->with($this->transaction->payeeId())->andReturn($this->payeeWallet);
    });

    $this->provider = mock(AdapterProviderInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('authorizeTransaction')->andReturn(true);
    });

    $this->provider = mock(AdapterProviderInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('authorizeTransaction')->andReturn(true);
    });

    $this->callSMS = mock(AdapterSMSNotification::class, function (MockInterface $mock) {
        $mock->shouldReceive('sendSMSNotification')->andReturn(true);
    });

    $this->callMail = mock(AdapterEmailNotification::class, function (MockInterface $mock) {
        $mock->shouldReceive('sendEmailNotification')->andReturn(true);
    });
});

it("should create transaction between users", function () {
    expect($this->payerWallet->getBalance())->toBe(500.0);
    expect($this->payeeWallet->getBalance())->toBe(0.0);

    expect($this->payer->type)->toBeInstanceOf(TypeUserEnum::class);
    expect($this->payer->type->value)->toBe(TypeUserEnum::CUSTOMER->value);

    expect($this->payee->type)->toBeInstanceOf(TypeUserEnum::class);
    expect($this->payee->type->value)->toBe(TypeUserEnum::RETAILER->value);

    expect($this->transaction->status)->toBeInstanceOf(TransactionStatusEnum::class);
    expect($this->transaction->status->value)->toBe(TransactionStatusEnum::CREATED->value);

    $walletServiceMock = new WalletService($this->walletRepositoryMock);

    $providerTransaction = new CallProvider($this->provider);
    $callMail            = new CallEmailNotification($this->callMail);
    $callSMS             = new CallSMSNotification($this->callSMS);

    $transactionService = new TransactionService(
        $this->transactionRepositoryMock,
        $walletServiceMock,
        $providerTransaction->getAdapter(),
        $callMail->getAdapter(),
        $callSMS->getAdapter()
    );

    $transactionService->create($this->transaction);

    $foundTransaction = $this->transactionRepositoryMock->findByIdTransaction($this->transaction->id->get());

    expect($this->payerWallet->getBalance())->toBe(400.0);
    expect($this->payeeWallet->getBalance())->toBe(100.0);

    expect($this->transaction->status)->toBeInstanceOf(TransactionStatusEnum::class);
    expect($this->transaction->status->value)->toBe(TransactionStatusEnum::COMPLETED->value);

    expect($foundTransaction)->toBe($this->transaction);
});

test('should throw an exception with insufficient balance create transaction between users', function () {
    $walletRepositoryMock = mock(WalletRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('getWalletByPayerId')->with($this->transaction->payerId())->andReturn(new Wallet(
            id: null,
            userType: $this->payer->type,
            userId: $this->payer->id,
            balance: 0.0
        ));

        $mock->shouldReceive('getWalletByPayeeId')->with($this->transaction->payeeId())->andReturn(
            new Wallet(
                id: null,
                userType: $this->payer->type,
                userId: $this->payer->id,
                balance: 0.0
            )
        );
    });

    $walletServiceMock = new WalletService($walletRepositoryMock);

    $providerTransaction = new CallProvider($this->provider);
    $callMail            = new CallEmailNotification($this->callMail);
    $callSMS             = new CallSMSNotification($this->callSMS);

    $transactionService = new TransactionService(
        $this->transactionRepositoryMock,
        $walletServiceMock,
        $providerTransaction->getAdapter(),
        $callMail->getAdapter(),
        $callSMS->getAdapter()
    );

    $transactionService->create($this->transaction);

    expect($this->payerWallet->getBalance())->toBe(0.0);
    expect($this->payeeWallet->getBalance())->toBe(0.0);

    expect($this->transaction->status)->toBeInstanceOf(TransactionStatusEnum::class);
    expect($this->transaction->status->value)->toBe(TransactionStatusEnum::CANCELED->value);
})->throws(InsufficientBalanceException::class);

test('should throw an exception when Shopkeeper tries to make a transaction', function () {
    $walletRepositoryMock = mock(WalletRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('getWalletByPayerId')->with($this->transaction->payerId())->andReturn(new Wallet(
            id: null,
            userType: TypeUserEnum::RETAILER,
            userId: $this->payer->id,
            balance: 600.0
        ));

        $mock->shouldReceive('getWalletByPayeeId')->with($this->transaction->payeeId())->andReturn(
            new Wallet(
                id: null,
                userType: $this->payer->type,
                userId: $this->payer->id,
                balance: 0.0
            )
        );
    });

    $walletServiceMock = new WalletService($walletRepositoryMock);

    $providerTransaction = new CallProvider($this->provider);
    $callMail            = new CallEmailNotification($this->callMail);
    $callSMS             = new CallSMSNotification($this->callSMS);

    $transactionService = new TransactionService(
        $this->transactionRepositoryMock,
        $walletServiceMock,
        $providerTransaction->getAdapter(),
        $callMail->getAdapter(),
        $callSMS->getAdapter()
    );

    $transactionService->create($this->transaction);

    expect($this->payerWallet->getBalance())->toBe(600.0);
    expect($this->payeeWallet->getBalance())->toBe(0.0);

    expect($this->transaction->status)->toBeInstanceOf(TransactionStatusEnum::class);
    expect($this->transaction->status->value)->toBe(TransactionStatusEnum::CANCELED->value);
})->throws(RetailerNotAllowedToPayException::class);

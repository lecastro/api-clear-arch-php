<?php

use Faker\Factory;
use Mockery\MockInterface;
use Domain\User\Domain\Entities\User;
use Domain\Wallet\Domain\Entities\Wallet;
use Domain\Shared\Enums\TypeUserEnum;
use Domain\Wallet\Domain\Service\WalletService;
use Domain\Shared\ValueObjects\Document;
use Domain\Transaction\Domain\Entities\Transaction;
use Domain\Transaction\Domain\Service\TransactionService;
use Domain\Transaction\Domain\Enums\TransactionStatusEnum;
use Domain\Wallet\Domain\Repository\WalletRepositoryInterface;
use Domain\Transaction\Domain\Repository\TransactionRepositoryInterface;
use Domain\Transaction\Domain\validator\Exceptions\InsufficientBalanceException;
use Domain\Transaction\Domain\validator\Exceptions\RetailerNotAllowedToPayException;
use Domain\User\Domain\validator\Exceptions\NegativeBalanceException;

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
});

it("should create transaction between users", function () {
    $transactionRepositoryMock = mock(TransactionRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('create')->with($this->transaction)->once();
        $mock->shouldReceive('findByIdTransaction')->with($this->transaction->id->get())->andReturn($this->transaction);
    });

    $walletRepositoryMock = mock(WalletRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('getWalletByPayerId')->with($this->transaction->payerId())->andReturn($this->payerWallet);
        $mock->shouldReceive('getWalletByPayeeId')->with($this->transaction->payeeId())->andReturn($this->payeeWallet);
    });

    expect($this->payerWallet->getBalance())->toBe(500.0);
    expect($this->payeeWallet->getBalance())->toBe(0.0);

    expect($this->payer->type)->toBeInstanceOf(TypeUserEnum::class);
    expect($this->payer->type->value)->toBe(TypeUserEnum::CUSTOMER->value);

    expect($this->payee->type)->toBeInstanceOf(TypeUserEnum::class);
    expect($this->payee->type->value)->toBe(TypeUserEnum::RETAILER->value);

    expect($this->transaction->status)->toBeInstanceOf(TransactionStatusEnum::class);
    expect($this->transaction->status->value)->toBe(TransactionStatusEnum::CREATED->value);

    $walletServiceMock = new WalletService($walletRepositoryMock);

    $transactionService = new TransactionService($transactionRepositoryMock, $walletServiceMock);

    $transactionService->create($this->transaction);

    $foundTransaction = $transactionRepositoryMock->findByIdTransaction($this->transaction->id->get());

    expect($this->payerWallet->getBalance())->toBe(400.0);
    expect($this->payeeWallet->getBalance())->toBe(100.0);
    
    expect($this->transaction->status)->toBeInstanceOf(TransactionStatusEnum::class);
    expect($this->transaction->status->value)->toBe(TransactionStatusEnum::COMPLETED->value);

    expect($foundTransaction)->toBe($this->transaction);
});

test('should throw an exception with insufficient balance create transaction between users', function () {
    $transactionRepositoryMock = mock(TransactionRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('create')->with($this->transaction);
    });

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

    $transactionService = new TransactionService($transactionRepositoryMock, $walletServiceMock);

    $transactionService->create($this->transaction);

    expect($this->payerWallet->getBalance())->toBe(0.0);
    expect($this->payeeWallet->getBalance())->toBe(0.0);

    expect($this->transaction->status)->toBeInstanceOf(TransactionStatusEnum::class);
    expect($this->transaction->status->value)->toBe(TransactionStatusEnum::CANCELED->value);
})->throws(InsufficientBalanceException::class);

test('should throw an exception when Shopkeeper tries to make a transaction', function () {
    $transactionRepositoryMock = mock(TransactionRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('create')->with($this->transaction);
    });

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

    $transactionService = new TransactionService($transactionRepositoryMock, $walletServiceMock);

    $transactionService->create($this->transaction);

    expect($this->payerWallet->getBalance())->toBe(600.0);
    expect($this->payeeWallet->getBalance())->toBe(0.0);

    expect($this->transaction->status)->toBeInstanceOf(TransactionStatusEnum::class);
    expect($this->transaction->status->value)->toBe(TransactionStatusEnum::CANCELED->value);
})->throws(RetailerNotAllowedToPayException::class);

test('should throw an exception when trying negative value', function () {
    $transactionRepositoryMock = mock(TransactionRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('create')->with($this->transaction);
    });

    $walletRepositoryMock = mock(WalletRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('getWalletByPayerId')->with($this->transaction->payerId())->andReturn(new Wallet(
            id: null,
            userType: TypeUserEnum::CUSTOMER,
            userId: $this->payer->id,
            balance: -600.0
        ));

        $mock->shouldReceive('getWalletByPayeeId')->with($this->transaction->payeeId())->andReturn(
            new Wallet(
                id: null,
                userType: TypeUserEnum::RETAILER,
                userId: $this->payer->id,
                balance: 0.0
            )
        );
    });

    $walletServiceMock = new WalletService($walletRepositoryMock);

    $transactionService = new TransactionService($transactionRepositoryMock, $walletServiceMock);

    $transactionService->create($this->transaction);

    expect($this->payerWallet->getBalance())->toBe(-600.0);
    expect($this->payeeWallet->getBalance())->toBe(0.0);

    expect($this->transaction->status)->toBeInstanceOf(TransactionStatusEnum::class);
    expect($this->transaction->status->value)->toBe(TransactionStatusEnum::CANCELED->value);
})->throws(NegativeBalanceException::class);

<?php

use Faker\Factory;
use Mockery\MockInterface;
use Domain\User\Domain\Entities\User;
use Domain\Wallet\Domain\Entities\Wallet;
use Domain\Shared\ValueObjects\Uuid;
use Domain\Shared\Enums\TypeUserEnum;
use Domain\Wallet\Domain\Service\WalletService;
use Domain\Shared\ValueObjects\Document;
use Domain\Wallet\Domain\Repository\WalletRepositoryInterface;
use Domain\User\Domain\validator\Exceptions\NegativeBalanceException;
use Domain\User\Domain\validator\Exceptions\InsufficientBalanceException;

beforeEach(function () {
    $this->faker = Factory::create();

    $this->user = new User(
        id: null,
        name: $this->faker->name(),
        email: $this->faker->email(),
        document: new Document('123.456.789-09'),
        password: '1234567',
        type: TypeUserEnum::CUSTOMER,
    );

    $this->wallet = new Wallet(
        id: null,
        userType: $this->faker->randomElement(
            [TypeUserEnum::CUSTOMER, TypeUserEnum::RETAILER]
        ),
        userId: $this->user->id,
        balance: 0
    );
});

it("should create a wallet", function () {
    $walletRepository = mock(WalletRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('create')->with($this->user)->andReturn($this->wallet);
        $mock->shouldReceive('findByWalletByUserId')->with($this->user->id->get())->andReturn(null);
    });

    $walletService = new WalletService($walletRepository);

    $wallet = $walletService->create($this->user);

    expect($wallet)->toBe($this->wallet);
});

it("should create returns if a wallet exists", function () {
    $existingUser = new Wallet(
        id: null,
        userType: TypeUserEnum::RETAILER,
        userId: Uuid::random(),
        balance: $this->faker->randomFloat(2, 0, 10000)
    );

    $walletRepository = mock(WalletRepositoryInterface::class, function (MockInterface $mock) use ($existingUser) {
        $mock->shouldReceive('create')->with($this->user)->andReturn($this->wallet);
        $mock->shouldReceive('findByWalletByUserId')->with($this->user->id->get())->andReturn($existingUser);
    });

    $walletService = new WalletService($walletRepository);

    $wallet = $walletService->create($this->user);

    expect($wallet)->toBe($existingUser);
});

it("should search wallet by wallet", function () {
    $walletRepository = mock(WalletRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('findById')->with($this->wallet->id->get())->andReturn($this->wallet);
    });

    $walletService = new WalletService($walletRepository);

    $wallet = $walletService->findById($this->wallet->id->get());

    expect($wallet)->toBe($this->wallet);
});

it("should return null when wallet does not exist", function () {
    $walletRepository = mock(WalletRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('findById')->with($this->wallet->id->get())->andReturn(null);
    });

    $walletService = new WalletService($walletRepository);

    $wallet = $walletService->findById($this->wallet->id->get());

    expect($wallet)->toBeNull();
});

it("should verify if the balance is available", function () {
    $walletRepository = mock(WalletRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('findById')->with($this->wallet->id->get())->andReturn($this->wallet);
    });

    $walletService = new WalletService($walletRepository);

    $walletWithBalance = $walletService->hasBalance($this->wallet->id->get(), 20.0);

    expect($walletWithBalance)->toBeTrue();

    $walletWithOutBalence = $walletService->hasBalance($this->wallet->id->get(), 0);

    expect($walletWithOutBalence)->toBeFalse();
});

it("should check if you have a balance", function () {
    $walletRepository = mock(WalletRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('findById')->with($this->wallet->id->get())->andReturn($this->wallet);
    });

    $walletService = new WalletService($walletRepository);

    $balance = $walletService->getBalance($this->wallet->id->get());

    expect($balance)->toBe(0.0);
});

it("should ensure the balance is correct after a deposit", function () {
    $walletRepository = mock(WalletRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('findById')->with($this->wallet->id->get())->andReturn($this->wallet);
    });

    $walletService = new WalletService($walletRepository);

    $walletService->deposit($this->wallet->id->get(), 100.0);

    $balance = $walletService->getBalance($this->wallet->id->get());

    expect($balance)->toBe(100.00);
});

test('should throw an exception if the deposit value is negative', function () {
    $walletRepository = mock(WalletRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('findById')->with($this->wallet->id->get())->andReturn($this->wallet);
    });

    $walletService = new WalletService($walletRepository);

    $walletService->deposit($this->wallet->id->get(), -100.0);
})->throws(NegativeBalanceException::class);

it("should ensure the balance is correct after a withdrawal", function () {
    $walletRepository = mock(WalletRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('findById')->with($this->wallet->id->get())->andReturn($this->wallet);
    });

    $walletService = new WalletService($walletRepository);

    $walletService->deposit($this->wallet->id->get(), 100.0);
    $walletService->withdrawal($this->wallet->id->get(), 50.0);

    $balance = $walletService->getBalance($this->wallet->id->get());

    expect($balance)->toBe(50.00);
});

test('should throw an exception if the withdrawal is greater than the balance', function () {
    $walletRepository = mock(WalletRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('findById')->with($this->wallet->id->get())->andReturn($this->wallet);
    });

    $walletService = new WalletService($walletRepository);

    $walletService->deposit($this->wallet->id->get(), 100.0);
    $walletService->withdrawal($this->wallet->id->get(), 150.0);
})->throws(InsufficientBalanceException::class);

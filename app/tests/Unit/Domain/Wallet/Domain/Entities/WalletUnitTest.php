<?php

use DateTime;
use Faker\Factory;
use Domain\User\Domain\Entities\User;
use Domain\Wallet\Domain\Entities\Wallet;
use Domain\Shared\ValueObjects\Uuid;
use Domain\Shared\Enums\TypeUserEnum;
use Domain\Shared\ValueObjects\Document;
use Domain\User\Domain\validator\Exceptions\NegativeBalanceException;
use Domain\User\Domain\validator\Exceptions\InsufficientBalanceException;

beforeEach(function () {
    $this->faker    = Factory::create();
    $this->balance  = 100.0;

    $this->user = new User(
        id: null,
        name: $this->faker->name(),
        email: $this->faker->email(),
        document: new Document('123.456.789-09'),
        password: $this->faker->password(),
        type: TypeUserEnum::CUSTOMER,
    );

    $this->wallet = new Wallet(
        id: null,
        userType: $this->faker->randomElement([TypeUserEnum::CUSTOMER, TypeUserEnum::RETAILER]),
        userId: $this->user->id,
        balance: 100.0
    );
});

test('should constructor of user wallet', function () {
    expect($this->wallet)->toHaveProperties([
        'id',
        'userType',
        'userId',
        'balance',
        'createdAt'
    ]);

    expect($this->wallet->id)->not->toBeNull();
    expect($this->wallet->id)->toBeInstanceOf(Uuid::class);
    expect($this->wallet->id->get())->toBeString();

    expect($this->wallet->userType)->toBeInstanceOf(TypeUserEnum::class);

    expect($this->wallet->userId)->not->toBeNull();
    expect($this->wallet->userId->get())->toBeString();
    expect($this->wallet->userId)->toBeInstanceOf(Uuid::class);

    expect($this->wallet->getBalance())->toBe($this->balance);

    expect($this->wallet->createdAt)->not->toBeNull();
    expect($this->wallet->createdAt)->toBeInstanceOf(DateTime::class);
});

it('should checks if hasBalance returns the correct result', function () {
    $wallet = new Wallet(
        null,
        $this->faker->randomElement([TypeUserEnum::CUSTOMER, TypeUserEnum::RETAILER]),
        $this->user->id,
        100.0
    );

    expect($wallet->hasBalance(50.0))->toBeFalse();

    expect($wallet->hasBalance(150.0))->toBeTrue();

    expect($wallet->hasBalance(100.0))->toBeFalse();
});

it('should check if the deposit methods work correctly', function () {
    $this->wallet->deposit(50.0);

    expect($this->wallet->getBalance())->toBe(150.0);
});

test('throw an exception if the deposit value is negative', function () {
    $this->wallet->deposit(-50.0);
})->throws(NegativeBalanceException::class);

it('should check if the withdrawal methods work correctly', function () {
    $this->wallet->withdrawal(20.0);

    expect($this->wallet->getBalance())->toBe(80.0);
});

test('throws an exception if the withdrawal amount is greater than the balance', function () {
    $this->wallet->withdrawal(250.0);
})->throws(InsufficientBalanceException::class);

<?php

use DateTime;
use Faker\Factory;
use Core\User\Domain\Entities\User;
use Core\Wallet\Domain\Entities\Wallet;
use Core\SeedWork\Domain\ValueObjects\Uuid;
use Core\SeedWork\Domain\Enums\TypeUserEnum;
use Core\SeedWork\Domain\ValueObjects\Document;

beforeEach(function () {
    $this->faker    = Factory::create();
    $this->balance  = $this->faker->randomFloat(2, 0, 10000);

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
        balance: $this->balance
    );
});

test('constructor of user wallet', function () {
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

    expect($this->wallet->balance)->toBe($this->balance);

    expect($this->wallet->createdAt)->not->toBeNull();
    expect($this->wallet->createdAt)->toBeInstanceOf(DateTime::class);
});

it('checks if hasBalance returns the correct result', function () {
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

<?php

use Domain\User\Domain\Entities\User;
use Domain\Shared\ValueObjects\Uuid;
use Domain\Shared\Enums\TypeUserEnum;
use Domain\Shared\ValueObjects\Document;
use Domain\Transaction\Domain\Entities\Transaction;
use Domain\Transaction\Domain\Enums\TransactionStatusEnum;
use Domain\Transaction\Domain\validator\Exceptions\NegativeBalanceException;
use Domain\Transaction\Domain\validator\Exceptions\EntityValidationException;

beforeEach(function () {
    $this->user = new User(
        id: null,
        name: 'userTest',
        email: 'user@test.com',
        document: new Document('123.456.789-09'),
        password: '1234567',
        type: TypeUserEnum::CUSTOMER,
    );

    $this->retailer = new User(
        id: null,
        name: 'retailerTest',
        email: 'retailer@test.com',
        document: new Document('22.459.895/0001-60'),
        password: '1234567',
        type: TypeUserEnum::RETAILER,
    );

    $this->transaction = new Transaction(
        id: null,
        payerId: $this->user->id,
        payeeId: $this->retailer->id,
        amount: 100.0,
        status: TransactionStatusEnum::CREATED
    );
});

test('constructor of transaction', function () {
    expect($this->transaction)->toHaveProperties([
        'id',
        'payerId',
        'payeeId',
        'amount',
        'status',
        'createdAt'
    ]);

    expect($this->transaction->id)->not->toBeNull();
    expect($this->transaction->id)->toBeInstanceOf(Uuid::class);
    expect($this->transaction->id->get())->toBeString();

    expect($this->transaction->payerId)->not->toBeNull();
    expect($this->transaction->payerId)->toBeInstanceOf(Uuid::class);
    expect($this->transaction->payerId->get())->toBeString();

    expect($this->transaction->payeeId)->not->toBeNull();
    expect($this->transaction->payeeId)->toBeInstanceOf(Uuid::class);
    expect($this->transaction->payeeId->get())->toBeString();

    expect($this->transaction->amount)->toBe(100.0);

    expect($this->transaction->status)->toBeInstanceOf(TransactionStatusEnum::class);

    expect($this->transaction->createdAt)->not->toBeNull();
    expect($this->transaction->createdAt)->toBeInstanceOf(DateTime::class);
});

test('should throw exception with amount is invalid', function () {
    new Transaction(
        id: null,
        payerId: $this->user->id,
        payeeId: $this->retailer->id,
        amount: -100.0,
        status: TransactionStatusEnum::CREATED
    );
})->throws(NegativeBalanceException::class);

test('should throw exception with status is invalid', function () {
    new Transaction(
        id: null,
        payerId: $this->user->id,
        payeeId: $this->retailer->id,
        amount: 100.0,
        status: TransactionStatusEnum::DEFAULT
    );
})->throws(EntityValidationException::class);

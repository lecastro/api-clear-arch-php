<?php

use DateTime;
use Faker\Factory;
use Domain\User\Domain\Entities\User;
use Domain\Shared\Enums\TypeUserEnum;
use Domain\Shared\ValueObjects\Document;
use Domain\Shared\Exceptions\EntityValidationException;

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
});

test('constructor of user and getters and setters', function () {

    expect($this->user)->toHaveProperties([
        'id',
        'name',
        'email',
        'document',
        'password',
        'type',
        'createdAt',
    ]);

    expect($this->user->id)->not->toBeNull();

    expect($this->user->name)->not->toBeNull();
    expect($this->user->name)->toBeString();
    expect($this->user->name)->toBe('userTest');

    expect($this->user->email)->not->toBeNull();
    expect($this->user->email)->toBeString();
    expect($this->user->email)->toBe('user@test.com');

    // todo test password

    expect($this->user->createdAt)->not->toBeNull();
    expect($this->user->createdAt)->toBeInstanceOf(DateTime::class);
});

test('constructor of retailer and getters and setters', function () {

    expect($this->retailer)->toHaveProperties([
        'id',
        'name',
        'email',
        'document',
        'password',
        'type',
        'createdAt',
    ]);

    expect($this->retailer->id)->not->toBeNull();

    expect($this->retailer->name)->not->toBeNull();
    expect($this->retailer->name)->toBeString();
    expect($this->retailer->name)->toBe('retailerTest');

    expect($this->retailer->email)->not->toBeNull();
    expect($this->retailer->email)->toBeString();
    expect($this->retailer->email)->toBe('retailer@test.com');

    // todo test password

    expect($this->retailer->createdAt)->not->toBeNull();
    expect($this->retailer->createdAt)->toBeInstanceOf(DateTime::class);
});

test('should throw exception with name is invalid - min characters', function () {
    new User(
        id: null,
        name: 'us',
        email: 'user@test.com',
        document: new Document('123.456.789-09'),
        password: '1234567',
        type: TypeUserEnum::CUSTOMER,
    );
})->throws(EntityValidationException::class);

test('should throw exception with name is invalid - max characters', function () {
    $name = Factory::create()->sentence(400);
    new User(
        id: null,
        name: $name,
        email: 'user@test.com',
        document: new Document('123.456.789-09'),
        password: '1234567',
        type: TypeUserEnum::CUSTOMER,
    );
})->throws(EntityValidationException::class);

test('should throw exception with email is invalid', function () {
    new User(
        id: null,
        name: 'userTest',
        email: 'user123',
        document: new Document('123.456.789-09'),
        password: '1234567',
        type: TypeUserEnum::CUSTOMER,
    );
})->throws(EntityValidationException::class);

test('should throw exception with type is invalid', function () {
    new User(
        id: null,
        name: 'userTest',
        email: 'user123',
        document: new Document('123.456.789-09'),
        password: '1234567',
        type: TypeUserEnum::isValid('user'),
    );

})->throws(EntityValidationException::class);

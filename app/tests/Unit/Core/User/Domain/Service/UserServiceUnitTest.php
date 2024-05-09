<?php

use Mockery\MockInterface;
use Core\User\Domain\Entities\User;
use Core\User\Domain\Service\UserService;
use Core\SeedWork\Domain\Enums\TypeUserEnum;
use Core\SeedWork\Domain\ValueObjects\Document;
use Core\User\Domain\Repository\UserRepositoryInterface;
use Core\User\Domain\validator\Exceptions\CPFAlreadyExistsException;
use Core\User\Domain\validator\Exceptions\EmailAlreadyExistsException;

beforeEach(function () {
    $this->user = new User(
        id: null,
        name: 'userTest',
        email: 'user@test.com',
        document: new Document('123.456.789-09'),
        password: '1234567',
        type: TypeUserEnum::CUSTOMER,
    );
});

it('check if the email does not exist', function () {
    $userRepositoryMock = mock(UserRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('findByEmail')->with('user@test.com')->andReturn(null);
    });

    $userService = new UserService($userRepositoryMock);

    expect($userService->checkIfEmailExists('user@test.com'))->toBeFalse();
});

it('check if the email exist', function () {
    $userRepositoryMock = mock(UserRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('findByEmail')->with('user@test.com')->andReturn($this->user);
    });

    $userService = new UserService($userRepositoryMock);

    expect(function () use ($userService) {
        $userService->checkIfEmailExists('user@test.com');
    })->toThrow(EmailAlreadyExistsException::class);
});

it('check if the CPF does not exist', function () {
    $userRepositoryMock = mock(UserRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('findByCPF')->with('12345678909')->andReturn(null);
    });

    $userService = new UserService($userRepositoryMock);

    expect($userService->checkIfCPFExists('12345678909'))->toBeFalse();
});

it('check if the CPF exist', function () {
    $userRepositoryMock = mock(UserRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('findByCPF')->with('12345678909')->andReturn($this->user);
    });

    $userService = new UserService($userRepositoryMock);

    expect(function () use ($userService) {
        $userService->checkIfCPFExists('12345678909');
    })->toThrow(CPFAlreadyExistsException::class);
});

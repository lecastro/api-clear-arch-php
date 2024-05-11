<?php

use Mockery\MockInterface;
use Domain\User\Domain\Entities\User;
use Domain\User\Domain\Service\UserService;
use Domain\Shared\Enums\TypeUserEnum;
use Domain\Shared\ValueObjects\Document;
use Domain\User\Domain\Repository\UserRepositoryInterface;
use Domain\User\Domain\validator\Exceptions\CPFAlreadyExistsException;
use Domain\User\Domain\validator\Exceptions\EmailAlreadyExistsException;

beforeEach(function () {
    $this->user = new User(
        id: null,
        name: 'userTest',
        email: 'user@test.com',
        document: new Document('12345678909'),
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

    expect($userService->checkIfEmailExists('user@test.com'))->toBeTrue();
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

    expect($userService->checkIfCPFExists('12345678909'))->toBeTrue();
});

it("should create a user", function () {
    $repositoryMock = mock(UserRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('create')->with($this->user)->once();
        $mock->shouldReceive('findByEmail')->with($this->user->email())->andReturn(null);
        $mock->shouldReceive('findByCPF')->with($this->user->document())->andReturn(null);
    });

    $userRepositoryMock = mock(UserRepositoryInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('findByEmail')->with($this->user->email())->andReturn($this->user);
    });

    $userService = new UserService($repositoryMock);

    $userService->create($this->user);

    $foundUser = $userRepositoryMock->findByEmail('user@test.com');

    expect($foundUser)->toBe($this->user);
});

it("should trigger an exception when creating a user email that already exists", function () {
    $repositoryMock = \Mockery::mock(UserRepositoryInterface::class);
    $repositoryMock->shouldReceive('findByEmail')->with($this->user->email())->andReturn($this->user);
    $repositoryMock->shouldReceive('findByCPF')->with($this->user->document())->andReturn(null);
    $repositoryMock->shouldReceive('create')->never();

    $userService = new UserService($repositoryMock);

    expect(function () use ($userService) {
        $userService->create($this->user);
    })->toThrow(EmailAlreadyExistsException::class);
});

it("should trigger an exception when creating a user CPF that already exists", function () {
    $repositoryMock = \Mockery::mock(UserRepositoryInterface::class);
    $repositoryMock->shouldReceive('findByEmail')->with($this->user->email())->andReturn(null);
    $repositoryMock->shouldReceive('findByCPF')->with($this->user->document())->andReturn($this->user);
    $repositoryMock->shouldReceive('create')->never();

    $userService = new UserService($repositoryMock);

    expect(function () use ($userService) {
        $userService->create($this->user);
    })->toThrow(CPFAlreadyExistsException::class);
});

<?php

use Core\User\Application\DTO\{
    InputUserDTO,
    OutputUserDTO
};
use Core\User\Domain\Service\UserService;
use Core\User\Domain\Repository\UserRepositoryInterface;
use Core\User\Application\UseCase\Create\CreateUserUseCase;

it("should create a user", function () {
    $userRepositoryMock = \Mockery::mock(UserRepositoryInterface::class);
    $userRepositoryMock->shouldReceive('create')->once();
    $userRepositoryMock->shouldReceive('findByEmail')->with('user@test.com')->andReturn(null);
    $userRepositoryMock->shouldReceive('findByCPF')->with('12345678909')->andReturn(null);

    $userService = new UserService($userRepositoryMock);

    $createUserUseCase = new CreateUserUseCase($userService);

    $inputDto = new InputUserDTO(
        name: 'userTest',
        email: 'user@test.com',
        document: '12345678909',
        password: '1234567',
        type: 'customer',
    );

    $outputUserDTO = $createUserUseCase->execute($inputDto);

    expect($outputUserDTO)->toBeInstanceOf(OutputUserDTO::class);
    expect($outputUserDTO->id)->not->toBeNull();
    expect($outputUserDTO->id)->toBeString();
    expect($outputUserDTO->name)->toBe('userTest');
    expect($outputUserDTO->email)->toBe('user@test.com');
    expect($outputUserDTO->document)->toBe('12345678909');
    expect($outputUserDTO->password)->toBe('1234567');
    expect($outputUserDTO->type)->toBe('customer');
    expect($outputUserDTO->createdAt)->not->toBeNull();
});

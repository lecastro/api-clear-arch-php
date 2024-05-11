<?php

namespace Usecase\User\Create;

use Domain\Shared\Enums\TypeUserEnum;
use Domain\User\Domain\Entities\User;
use Domain\Shared\ValueObjects\Document;
use Domain\User\Domain\Service\UserService;
use Usecase\User\Create\DTO\{
    InputUserDTO,
    OutputUserDTO
};

class CreateUserUseCase
{
    public function __construct(protected UserService $service)
    {
    }

    public function execute(InputUserDTO $input): OutputUserDTO
    {
        $user = new User(
            id: null,
            name: $input->name,
            email: $input->email,
            document: new Document($input->document),
            password: $input->password,
            type: TypeUserEnum::isValid($input->type),
        );

        $this->service->create($user);

        return new OutputUserDTO(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            document: $user->document,
            password: $user->password,
            type: $user->type->value,
            createdAt: $user->createdAt->format('Y-m-d H:i:s')
        );
    }
}

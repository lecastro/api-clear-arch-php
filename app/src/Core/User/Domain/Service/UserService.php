<?php

namespace Core\User\Domain\Service;

use Core\User\Domain\validator\UserValidation;
use Core\User\Domain\Repository\UserRepositoryInterface;

class UserService
{
    public function __construct(protected UserRepositoryInterface $repository)
    {
    }

    public function checkIfEmailExists(string $email): bool
    {
        $existingUser = $this->repository->findByEmail($email);

        if ($existingUser !== null) {
            UserValidation::validateEmail($email);
        }

        return false;
    }
    public function checkIfCPFExists(string $cpf): bool
    {
        $existingUser = $this->repository->findByCPF($cpf);

        if ($existingUser !== null) {
            UserValidation::validateCPF($cpf);
        }

        return false;
    }
}

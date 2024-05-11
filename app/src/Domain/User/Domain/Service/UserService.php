<?php

namespace Domain\User\Domain\Service;

use Domain\User\Domain\Entities\User;
use Domain\User\Domain\validator\UserValidation;
use Domain\User\Domain\Repository\UserRepositoryInterface;

class UserService
{
    public function __construct(protected UserRepositoryInterface $repository)
    {
    }
    
    public function checkIfEmailExists(string $email): bool
    {
        $existingUser = $this->repository->findByEmail($email);

        return $existingUser !== null;
    }

    public function checkIfCPFExists(string $cpf): bool
    {
        $existingUser = $this->repository->findByCPF($cpf);

        return $existingUser !== null;
    }

    public function create(User $user): void
    {
        try {
            if ($this->checkIfEmailExists($user->email())) {
                UserValidation::validateEmail($user->email());
            }

            if ($this->checkIfCPFExists($user->document())) {
                UserValidation::validateCPF($user->document());
            }
            $this->repository->create($user);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

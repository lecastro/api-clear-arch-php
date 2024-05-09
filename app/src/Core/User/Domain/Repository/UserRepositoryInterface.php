<?php

namespace Core\User\Domain\Repository;

use Core\User\Domain\Entities\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;
    public function findByCPF(string $cpf): ?User;
    public function create(User $user): void;
}

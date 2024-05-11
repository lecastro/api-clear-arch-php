<?php

namespace Usecase\User\Create\DTO;

class OutputUserDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $document,
        public readonly string $password,
        public readonly string $type,
        public readonly string $createdAt,
    ) {
    }
}

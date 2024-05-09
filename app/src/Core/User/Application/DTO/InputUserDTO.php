<?php

namespace Core\User\Application\DTO;

class InputUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $document,
        public readonly string $password,
        public readonly string $type,
    ) {
    }
}

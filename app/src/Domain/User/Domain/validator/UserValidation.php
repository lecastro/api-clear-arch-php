<?php

namespace Domain\User\Domain\validator;

use Domain\User\Domain\validator\Exceptions\CPFAlreadyExistsException;
use Domain\User\Domain\validator\Exceptions\EmailAlreadyExistsException;

class UserValidation
{
    public static function validateCPF($customMessage = null): void
    {
        throw new CPFAlreadyExistsException($customMessage ?? "User with the provided {$customMessage} already exists");
    }

    public static function validateEmail($customMessage = null): void
    {
        throw new EmailAlreadyExistsException($customMessage ?? "User with the provided {$customMessage} already exists");
    }
}

<?php

namespace Core\SeedWork\Domain\Validators;

use Core\SeedWork\Domain\Exceptions\EntityValidationException;

class DomainValidation
{
    public static function notNull(string $value = null, string $customMessage = null): void
    {
        if (empty($value)) {
            throw new EntityValidationException($customMessage ?? 'Should not be empty');
        }
    }

    public static function strMaxLength(string $value = null, int $length = 255, string $customMessage = null): void
    {
        if (strlen($value) > $length) {
            throw new EntityValidationException($customMessage ?? "The value must not be greater than {$length} characters");
        }
    }

    public static function strMinLength(string $value = null, int $length = 3, string $customMessage = null): void
    {
        if (strlen($value) <= $length) {
            throw new EntityValidationException($customMessage ?? "The value must be at least {$length} characters");
        }
    }

    public static function strCanNullAndMaxLength(string $value = null, int $length = 255, string $customMessage = null): void
    {
        if (!empty($value) && strlen($value) > $length) {
            throw new EntityValidationException($customMessage ?? "The value must not be greater than {$length} characters");
        }
    }

    public static function validateEmail(string $email = null, string $customMessage = null): void
    {
        if (!empty($email)) {
            if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
                throw new EntityValidationException($customMessage ?? "The email address provided is not valid");
            }
        }
    }
}

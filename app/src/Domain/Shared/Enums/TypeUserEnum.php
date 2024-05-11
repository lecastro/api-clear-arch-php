<?php

namespace Domain\Shared\Enums;

enum TypeUserEnum: string
{
    case CUSTOMER = 'customer';
    case RETAILER = 'retailer';
    case DEFAULT  = 'default';

    public static function isValid(string $value): self
    {
        if (self::CUSTOMER->value == $value) {
            return self::CUSTOMER;
        }

        if (self::RETAILER->value == $value) {
            return self::RETAILER;
        }

        return self::DEFAULT;
    }
}

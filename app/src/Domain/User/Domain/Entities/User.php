<?php

namespace Domain\User\Domain\Entities;

use DateTime;
use Domain\Shared\ValueObjects\Uuid;
use Domain\Shared\Enums\TypeUserEnum;
use Domain\Shared\ValueObjects\Document;
use Domain\Shared\Traits\MethodsMagicsTrait;
use Domain\Shared\Validators\DomainValidation;

class User
{
    use MethodsMagicsTrait;

    public function __construct(
        protected null|Uuid $id = null,
        protected string $name,
        protected string $email,
        protected Document $document,
        protected string $password,
        protected TypeUserEnum $type,
        protected ?DateTime $createdAt = null,
    ) {
        $this->id = $this->id ?? Uuid::random();
        $this->createdAt = $this->createdAt ?? new DateTime();

        $this->validate();
    }

    private function validate(): void
    {
        DomainValidation::strMinLength($this->name);
        DomainValidation::strMaxLength($this->name);

        DomainValidation::validateEmail($this->email);
        DomainValidation::validateType($this->type);
    }
}

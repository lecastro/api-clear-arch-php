<?php

namespace Domain\Shared\Traits;

trait MethodsMagicsTrait
{
    public function __get($name)
    {
        return $this->{$name};
    }

    public function createdAt(): string
    {
        return (string) $this->createdAt->format('Y-m-d H:i:s');
    }

    public function id(): string
    {
        return (string) $this->id;
    }

    public function email(): string
    {
        return (string) $this->email;
    }

    public function document(): string
    {
        return (string) $this->document;
    }

    public function payerId(): string
    {
        return (string) $this->payerId;
    }

    public function payeeId(): string
    {
        return (string) $this->payeeId;
    }

    public function amount(): float
    {
        return $this->amount;
    }
}

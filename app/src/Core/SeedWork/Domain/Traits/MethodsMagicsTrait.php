<?php

namespace Core\SeedWork\Domain\Traits;

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
}

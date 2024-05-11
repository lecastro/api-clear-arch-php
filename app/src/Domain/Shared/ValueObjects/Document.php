<?php

namespace Domain\Shared\ValueObjects;

use InvalidArgumentException;

class Document
{
    public function __construct(private string|null $document)
    {
        $this->isValid($document);
    }

    private function isValid(string|null $document): void
    {
        if (empty($document)) {
            throw new InvalidArgumentException(sprintf("<%s> does not allow the value <%s>", static::class, $document));
        }

        if (!self::isValidCPF($document) && !self::isValidCNPJ($document)) {
            throw new InvalidArgumentException(sprintf("<%s> does not allow the value <%s>", static::class, $document));
        }
    }

    private function isValidCNPJ(string $value): bool
    {
        $value = trim($value);

        $c = preg_replace('/\D/', '', $value);

        $b = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        if (strlen($c) != 14) {
            return false;
        } elseif (preg_match("/^{$c[0]}{14}$/", $c) > 0) {
            return false;
        }

        for ($i = 0, $n = 0; $i < 12; $n += $c[$i] * $b[++$i]);

        if ($c[12] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        for ($i = 0, $n = 0; $i <= 12; $n += $c[$i] * $b[$i++]);

        if ($c[13] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        return true;
    }
    private function isValidCPF(string $value): bool
    {
        $c = preg_replace('/\D/', '', trim($value));

        if (strlen($c) != 11 || preg_match("/^{$c[0]}{11}$/", $c)) {
            return false;
        }

        for ($s = 10, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--);

        if ($c[9] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        for ($s = 11, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--);

        if ($c[10] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        return true;
    }

    public function get(): string
    {
        return (string) $this->document;
    }

    public function __toString(): string
    {
        return $this->document;
    }
}

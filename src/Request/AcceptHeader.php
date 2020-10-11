<?php

declare(strict_types=1);

namespace TerryApiBundle\Request;

final class AcceptHeader
{
    public const NAME = 'Accept';
    private string $accept;

    private function __construct(string $accept)
    {
        $this->accept = $accept;
    }

    public static function fromString(string $accept): self
    {
        return new self($accept);
    }

    public function toString(): string
    {
        return $this->accept;
    }
}

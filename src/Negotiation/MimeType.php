<?php

declare(strict_types=1);

namespace Violines\RestBundle\Negotiation;

/**
 * @internal
 */
final class MimeType
{
    private string $mimeType;

    public function __construct(string $mimeType)
    {
        $this->mimeType = $mimeType;
    }

    public static function fromString(string $mimeType): self
    {
        return new self($mimeType);
    }

    public function toString(): string
    {
        return $this->mimeType;
    }
}

<?php

declare(strict_types=1);

namespace Violines\RestBundle\Request;

use Violines\RestBundle\Negotiation\MimeType;

/**
 * @internal
 */
final class ContentTypeHeader
{
    public const NAME = 'Content-Type';
    private string $contentType;

    private function __construct(string $contentType)
    {
        $this->contentType = $contentType;
    }

    public static function fromString(string $contentType): self
    {
        return new self($contentType);
    }

    public function toString(): string
    {
        return $this->contentType;
    }

    public function toMimeType(): MimeType
    {
        return MimeType::fromString($this->contentType);
    }
}

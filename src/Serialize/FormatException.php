<?php

declare(strict_types=1);

namespace Violines\RestBundle\Serialize;

use Violines\RestBundle\Negotiation\MimeType;

/**
 * @internal
 */
final class FormatException extends \RuntimeException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function notConfigured(MimeType $mimeType): self
    {
        return new self(\sprintf('MimeType %s was not configured for any Format. Check configuration under serialize > formats', $mimeType->toString()));
    }
}

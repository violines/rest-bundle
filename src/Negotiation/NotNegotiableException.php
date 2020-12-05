<?php

declare(strict_types=1);

namespace Violines\RestBundle\Negotiation;

final class NotNegotiableException extends \RuntimeException implements \Throwable
{
    private function __construct(string $message)
    {
        $this->message = $message;
    }

    public static function notConfigured(string $mimeTypes): self
    {
        return new self(sprintf('None of the accepted mimetypes %s are configured for any Format. Check configuration under serialize > formats', $mimeTypes));
    }
}

<?php

declare(strict_types=1);

namespace TerryApiBundle\Serialize;

use TerryApiBundle\Serialize\Format;

final class FormatException extends \RuntimeException implements \Throwable
{
    private function __construct(string $message)
    {
        $this->message = $message;
    }

    public static function notConfigured(Format $format)
    {
        return new self(sprintf('Format %s was not configured. Check bundle configuration under serialize > formats', $format->toString()));
    }
}

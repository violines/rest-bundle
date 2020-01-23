<?php

declare(strict_types=1);

namespace TerryApiBundle\Exception;

class RequestHeaderException extends \RuntimeException implements \Throwable
{
    public static function cannotProcess(string $key, string $value): self
    {
        return new self(
            sprintf('Value %s of Header %s cannot be processed.', $key, $value)
        );
    }
}

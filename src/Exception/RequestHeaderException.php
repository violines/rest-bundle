<?php

declare(strict_types=1);

namespace TerryApiBundle\Exception;

class RequestHeaderException extends \RuntimeException implements \Throwable
{
    private function __construct(string $message)
    {
        $this->message = $message;
    }

    public static function expected(string $key)
    {
        return new self(
            sprintf('The Header: %s is expected.', $key)
        );
    }

    public static function valueNotAllowed(string $key, string $value): self
    {
        return new self(
            sprintf('Value: %s of Header: %s is not allowed.', $value, $key)
        );
    }
}

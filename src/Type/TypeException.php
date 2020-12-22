<?php

declare(strict_types=1);

namespace Violines\RestBundle\Type;

/**
 * @internal
 */
final class TypeException extends \RuntimeException implements \Throwable
{
    private function __construct(string $message)
    {
        $this->message = $message;
    }

    public static function notObject(): self
    {
        return new self('The given Type is no object.');
    }

    public static function notSameClass(): self
    {
        return new self('Not all objects of given array are instances of the same class.');
    }
}

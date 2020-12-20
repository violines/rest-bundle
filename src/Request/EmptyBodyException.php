<?php

declare(strict_types=1);

namespace Violines\RestBundle\Request;

use Symfony\Component\HttpFoundation\Response;
use Violines\RestBundle\Error\Error;
use Violines\RestBundle\Error\ErrorInterface;

/**
 * @internal
 */
final class EmptyBodyException extends \RuntimeException implements \Throwable, ErrorInterface
{
    public function getContent(): object
    {
        return Error::new($this->getMessage());
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public static function required(): self
    {
        return new self('The request body cannot be empty.');
    }
}

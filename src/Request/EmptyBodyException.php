<?php

declare(strict_types=1);

namespace TerryApiBundle\Request;

use Symfony\Component\HttpFoundation\Response;
use TerryApiBundle\Error\Error;
use TerryApiBundle\Error\ErrorInterface;

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

<?php

declare(strict_types=1);

namespace TerryApiBundle\Error;

use Symfony\Component\HttpFoundation\Response;
use TerryApiBundle\Error\ErrorInterface;
use TerryApiBundle\Error\Error;

final class RequestHeaderException extends \RuntimeException implements \Throwable, ErrorInterface
{
    private const TITLE = 'Request Header wrong';

    private function __construct(string $message)
    {
        $this->message = $message;
    }

    public static function expected(string $key): self
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

    public function getContent(): Error
    {
        return Error::new($this->message, self::TITLE);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}

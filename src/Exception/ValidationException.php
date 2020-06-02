<?php

declare(strict_types=1);

namespace TerryApiBundle\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use TerryApiBundle\HTTPApi\ValidationError;

class ValidationException extends \RuntimeException implements \Throwable
{
    private ConstraintViolationListInterface $violations;

    private function __construct(ConstraintViolationListInterface $violations)
    {
        $this->violations = $violations;
    }

    public static function create(ConstraintViolationListInterface $violations): self
    {
        return new self($violations);
    }

    public function violations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }

    public function getContent(): ValidationError
    {
        return ValidationError::fromViolations($this->violations);
    }

    public function getHTTPStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}

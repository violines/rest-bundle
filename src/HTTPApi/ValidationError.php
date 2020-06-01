<?php

declare(strict_types=1);

namespace TerryApiBundle\HTTPApi;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use TerryApiBundle\Annotation\HTTPApi;

/**
 * @HTTPApi
 */
class ValidationError
{
    public string $message = 'The request body contains invalid values.';

    public array $violations;

    private function __construct(array $violations)
    {
        $this->violations = $violations;
    }

    public static function fromViolations(ConstraintViolationListInterface $violations): self
    {
        $_violations = [];

        /** @var iterable<ConstraintViolationInterface> $violations */
        foreach ($violations as $violation) {
            $_violations[] = ValidationErrorViolation::fromViolation($violation);
        }

        return new self($_violations);
    }
}

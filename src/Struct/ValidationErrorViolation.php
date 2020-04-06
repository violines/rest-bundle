<?php

declare(strict_types=1);

namespace TerryApiBundle\Struct;

use Symfony\Component\Validator\ConstraintViolationInterface;
use TerryApiBundle\Annotation\Struct;

/**
 * @Struct
 */
class ValidationErrorViolation
{
    public string $property;

    public string $message;

    private function __construct(ConstraintViolationInterface $violation)
    {
        $this->property = (string) $violation->getPropertyPath();
        // can be removed after https://github.com/symfony/symfony/pull/34298 was released:
        /** @psalm-suppress PossiblyInvalidPropertyAssignmentValue */
        $this->message = $violation->getMessage();
    }

    public static function fromViolation(ConstraintViolationInterface $violation): self
    {
        return new self($violation);
    }
}

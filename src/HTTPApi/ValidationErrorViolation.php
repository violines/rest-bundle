<?php

declare(strict_types=1);

namespace TerryApiBundle\HTTPApi;

use Symfony\Component\Validator\ConstraintViolationInterface;
use TerryApiBundle\Annotation\HTTPApi;

/**
 * @HTTPApi
 */
class ValidationErrorViolation
{
    public string $property;

    public string $message;

    private function __construct(ConstraintViolationInterface $violation)
    {
        $this->property = (string) $violation->getPropertyPath();
        /**
         * @psalm-suppress PossiblyInvalidPropertyAssignmentValue
         * @psalm-suppress UndefinedDocblockClass
         */
        $this->message = (string) $violation->getMessage();
    }

    public static function fromViolation(ConstraintViolationInterface $violation): self
    {
        return new self($violation);
    }
}

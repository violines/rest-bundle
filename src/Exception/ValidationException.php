<?php

declare(strict_types=1);

namespace TerryApiBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends \RuntimeException implements \Throwable
{
    private ConstraintViolationListInterface $violations;

    public function __construct(ConstraintViolationListInterface $violations)
    {
        $this->violations = $violations;
    }

    public function violations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}

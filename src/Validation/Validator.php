<?php

declare(strict_types=1);

namespace TerryApiBundle\Validation;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use TerryApiBundle\Error\ValidationException;

final class Validator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param object[]|object $data
     */
    public function validate($data): void
    {
        $violations = $this->validator->validate($data);

        if (0 < count($violations)) {
            throw ValidationException::fromViolationList($violations);
        }
    }
}

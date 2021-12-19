<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Fake;

use Symfony\Component\Validator\ConstraintViolationInterface;

class ConstraintViolationFake implements ConstraintViolationInterface
{
    public function getMessage(): string
    {
        return 'message';
    }

    public function getMessageTemplate(): string
    {
        return 'message_tpl';
    }

    public function getParameters(): array
    {
        return [];
    }

    public function getPlural(): ?int
    {
        return null;
    }

    public function getRoot(): mixed
    {
        return null;
    }

    public function getPropertyPath(): string
    {
        return 'path';
    }

    public function getInvalidValue(): mixed
    {
        return null;
    }

    public function getCode(): ?string
    {
        return null;
    }
}

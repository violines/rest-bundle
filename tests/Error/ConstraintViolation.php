<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Error;

use Symfony\Component\Validator\ConstraintViolationInterface;

class ConstraintViolation implements ConstraintViolationInterface
{
    public function getMessage()
    {
        return 'message';
    }

    public function getMessageTemplate()
    {
    }
    public function getParameters()
    {
    }

    public function getPlural()
    {
    }

    public function getRoot()
    {
    }

    public function getPropertyPath()
    {
    }

    public function getInvalidValue()
    {
    }

    public function getCode()
    {
    }
}

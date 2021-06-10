<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Fake;

use Symfony\Component\Validator\ConstraintViolationInterface;

class ConstraintViolationFake implements ConstraintViolationInterface
{
    public function getMessage()
    {
        return 'message';
    }

    public function getMessageTemplate()
    {
        // test
    }

    public function getParameters()
    {
        // test
    }

    public function getPlural()
    {
        // test
    }

    public function getRoot()
    {
        // test
    }

    public function getPropertyPath()
    {
        // test
    }

    public function getInvalidValue()
    {
        // test
    }

    public function getCode()
    {
        // test
    }
}

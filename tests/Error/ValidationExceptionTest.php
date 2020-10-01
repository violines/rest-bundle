<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Error;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Error\ValidationException;

class ValidationExceptionTest extends TestCase
{
    public function testShouldCreateValidationException(): void
    {
        $exception = ValidationException::fromViolationList(new ConstraintViolationList());

        $this->assertInstanceOf(ValidationException::class, $exception);
    }

    public function testShouldReturnViolationList(): void
    {
        $violationList = new ConstraintViolationList();

        $exception = ValidationException::fromViolationList($violationList);

        $this->assertEquals($violationList, $exception->getViolationList());
    }

    public function testExceptionShouldReturnBadRequestHttpCode(): void
    {
        $exception = ValidationException::fromViolationList(new ConstraintViolationList());

        $this->assertEquals(400, $exception->getHttpStatusCode());
    }
}

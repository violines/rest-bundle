<?php

declare(strict_types=1);

namespace TerryApi\Tests\Exception;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Exception\ValidationException;
use TerryApiBundle\Tests\Stubs\ConstraintViolationList;

class ValidationExceptionTest extends TestCase
{
    public function testShouldCreateValidationException(): void
    {
        $exception = ValidationException::create(new ConstraintViolationList());

        $this->assertInstanceOf(ValidationException::class, $exception);
    }

    public function testShouldReturnViolationList(): void
    {
        $violationList = new ConstraintViolationList();

        $exception = ValidationException::create($violationList);

        $this->assertEquals($violationList, $exception->violations());
    }

    public function testExceptionShouldReturnBadRequestHttpCode(): void
    {
        $exception = ValidationException::create(new ConstraintViolationList());

        $this->assertEquals(400, $exception->httpStatusCode());
    }
}

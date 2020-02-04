<?php

declare(strict_types=1);

namespace TerryApi\Tests\Exception;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Exception\ValidationException;
use TerryApiBundle\Struct\ValidationError;
use TerryApiBundle\Tests\Stubs\ConstraintViolationListStub;

class ValidationExceptionTest extends TestCase
{
    public function testShouldCreateValidationException(): void
    {
        $exception = ValidationException::create(new ConstraintViolationListStub());

        $this->assertInstanceOf(ValidationException::class, $exception);
    }

    public function testShouldReturnViolationList(): void
    {
        $violationList = new ConstraintViolationListStub();

        $exception = ValidationException::create($violationList);

        $this->assertEquals($violationList, $exception->violations());
    }

    public function testExceptionShouldReturnValidationErrorStruct(): void
    {
        $exception = ValidationException::create(new ConstraintViolationListStub());

        $this->assertInstanceOf(ValidationError::class, $exception->getStruct());
    }

    public function testExceptionShouldReturnBadRequestHttpCode(): void
    {
        $exception = ValidationException::create(new ConstraintViolationListStub());

        $this->assertEquals(400, $exception->getHTTPStatusCode());
    }
}

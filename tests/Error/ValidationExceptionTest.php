<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Error;

use PHPUnit\Framework\TestCase;
use Violines\RestBundle\Error\ValidationException;
use Violines\RestBundle\Tests\Fake\ConstraintViolationListFake;

/**
 * @covers \Violines\RestBundle\Error\ValidationException
 */
class ValidationExceptionTest extends TestCase
{
    public function testShouldCreateValidationException(): void
    {
        $exception = ValidationException::fromViolationList(new ConstraintViolationListFake());

        $this->assertInstanceOf(ValidationException::class, $exception);
    }

    public function testShouldReturnViolationList(): void
    {
        $violationList = new ConstraintViolationListFake();

        $exception = ValidationException::fromViolationList($violationList);

        $this->assertEquals($violationList, $exception->getViolationList());
    }

    public function testExceptionShouldReturnBadRequestHttpCode(): void
    {
        $exception = ValidationException::fromViolationList(new ConstraintViolationListFake());

        $this->assertEquals(400, $exception->getStatusCode());
    }
}

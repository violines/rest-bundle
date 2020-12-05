<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use Violines\RestBundle\Error\Error;
use Violines\RestBundle\Request\EmptyBodyException;

/**
 * @covers \Violines\RestBundle\Request\EmptyBodyException
 */
class EmptyBodyExceptionTest extends TestCase
{
    public function testShouldCreateEmptyBodyException(): void
    {
        $this->assertInstanceOf(EmptyBodyException::class, EmptyBodyException::required());
    }

    public function testShouldReturnError(): void
    {
        $error = EmptyBodyException::required()->getContent();

        $this->assertInstanceOf(Error::class, $error);
        $this->assertEquals('The request body cannot be empty.', $error->getDetail());
    }

    public function testExceptionShouldReturnBadRequestHttpCode(): void
    {
        $exception = EmptyBodyException::required();

        $this->assertEquals(400, $exception->getStatusCode());
    }
}

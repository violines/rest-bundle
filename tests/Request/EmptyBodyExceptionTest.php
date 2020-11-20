<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Error;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Error\Error;
use TerryApiBundle\Request\EmptyBodyException;

/**
 * @covers \TerryApiBundle\Request\EmptyBodyException
 */
class EmptyBodyExceptionTest extends TestCase
{
    public function testShouldEmptyBodyException(): void
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

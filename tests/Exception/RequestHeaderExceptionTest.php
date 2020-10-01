<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Exception;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Exception\RequestHeaderException;

class RequestHeaderExceptionTest extends TestCase
{
    public function testShouldReturnExpect()
    {
        $exception = RequestHeaderException::expected('Key');

        $this->assertInstanceOf(RequestHeaderException::class, $exception);
    }

    public function testShouldReturnValueNotAllowed()
    {
        $exception = RequestHeaderException::valueNotAllowed('Key', 'Value');

        $this->assertInstanceOf(RequestHeaderException::class, $exception);
    }
}

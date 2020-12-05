<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\HttpApi;

use PHPUnit\Framework\TestCase;
use Violines\RestBundle\HttpApi\HttpApiParameterException;

/**
 * @covers \Violines\RestBundle\HttpApi\HttpApiParameterException
 */
class HttpApiParameterExceptionTest extends TestCase
{
    public function testShouldCreateAttributeEnumException(): void
    {
        $exception = HttpApiParameterException::enum('properyName', 'wrongValue', ['expected1', 'expected2']);

        $this->assertInstanceOf(HttpApiParameterException::class, $exception);
        $this->assertEquals('The value wrongValue for the parameter \'properyName\' for \'#[HttpApi]\' or \'@HttpApi\' is not allowed. Expected values: ["expected1","expected2"].', $exception->getMessage());
    }
}

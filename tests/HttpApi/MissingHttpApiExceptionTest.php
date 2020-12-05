<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\HttpApi;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\HttpApi\MissingHttpApiException;

/**
 * @covers \TerryApiBundle\HttpApi\MissingHttpApiException
 */
class MissingHttpApiExceptionTest extends TestCase
{
    public function testShouldStruct(): void
    {
        $exception = MissingHttpApiException::className('CustomClass');

        $this->assertInstanceOf(MissingHttpApiException::class, $exception);
        $this->assertEquals('\'#[HttpApi]\' or \'@HttpApi\' for CustomClass not found.', $exception->getMessage());
    }
}

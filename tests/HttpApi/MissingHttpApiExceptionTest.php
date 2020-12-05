<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\HttpApi;

use PHPUnit\Framework\TestCase;
use Violines\RestBundle\HttpApi\MissingHttpApiException;

/**
 * @covers \Violines\RestBundle\HttpApi\MissingHttpApiException
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

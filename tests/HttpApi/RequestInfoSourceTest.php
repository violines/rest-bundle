<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\HttpApi;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\HttpApi\HttpApi;
use TerryApiBundle\HttpApi\HttpApiParameterException;
use TerryApiBundle\HttpApi\RequestInfoSource;

/**
 * @covers \TerryApiBundle\HttpApi\RequestInfoSource
 */
class RequestInfoSourceTest extends TestCase
{
    public function testShouldCreateRequestInfoSource(): void
    {
        $requestInfoSource = RequestInfoSource::fromString(HttpApi::QUERY_STRING);

        $this->assertInstanceOf(RequestInfoSource::class, $requestInfoSource);
        $this->assertEquals(HttpApi::QUERY_STRING, $requestInfoSource->toString());
    }

    public function testShouldThrowHttpApiParameterException(): void
    {
        $this->expectException(HttpApiParameterException::class);

        RequestInfoSource::fromString('test');
    }
}

<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\HttpApi;

use PHPUnit\Framework\TestCase;
use Violines\RestBundle\HttpApi\HttpApi;
use Violines\RestBundle\HttpApi\HttpApiParameterException;
use Violines\RestBundle\HttpApi\RequestInfoSource;

/**
 * @covers \Violines\RestBundle\HttpApi\RequestInfoSource
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

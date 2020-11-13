<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\HttpApi;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use TerryApiBundle\HttpApi\AnnotationNotFoundException;
use TerryApiBundle\HttpApi\HttpApi;
use TerryApiBundle\HttpApi\HttpApiReader;
use TerryApiBundle\Tests\Stubs\Brownie;
use TerryApiBundle\Tests\Stubs\Candy;

/**
 * @covers \TerryApiBundle\HttpApi\HttpApiReader
 */
class HttpApiReaderTest extends TestCase
{
    private HTTPApiReader $httpApiReader;

    public function setUp(): void
    {
        $this->httpApiReader = new HttpApiReader(new AnnotationReader());
    }

    public function testShouldReturnStructAnnotation(): void
    {
        $this->assertInstanceOf(HttpApi::class, $this->httpApiReader->read(Candy::class));
    }

    public function testShouldThrowAnnotationNotFoundException(): void
    {
        $this->expectException(AnnotationNotFoundException::class);

        $this->httpApiReader->read(Brownie::class);
    }
}

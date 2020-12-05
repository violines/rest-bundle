<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\HttpApi;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use TerryApiBundle\HttpApi\AnnotationReaderNotInstalledException;
use TerryApiBundle\HttpApi\HttpApi;
use TerryApiBundle\HttpApi\HttpApiReader;
use TerryApiBundle\HttpApi\MissingHttpApiException;
use TerryApiBundle\Tests\Stubs\Brownie;
use TerryApiBundle\Tests\Stubs\Candy;

/**
 * @covers \TerryApiBundle\HttpApi\HttpApiReader
 */
class HttpApiReaderTest extends TestCase
{
    public function testShouldReturnHttpApiAnnotation(): void
    {
        $httpApiReader = new HttpApiReader(new AnnotationReader());

        $this->assertInstanceOf(HttpApi::class, $httpApiReader->read(Candy::class));
    }

    public function testShouldThrowAnnotationReaderNotInstalledException(): void
    {
        $this->expectException(AnnotationReaderNotInstalledException::class);

        $httpApiReader = new HttpApiReader();

        $httpApiReader->read(Brownie::class);
    }

    public function testShouldThrowMissingHttpApiException(): void
    {
        $httpApiReader = new HttpApiReader(new AnnotationReader());

        $this->expectException(MissingHttpApiException::class);

        $httpApiReader->read(Brownie::class);
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testShouldReturnHttpApiAttribute(): void
    {
        $httpApiReader = new HttpApiReader();

        $this->assertInstanceOf(HttpApi::class, $httpApiReader->read(HttpApiQueryString::class));
    }
}

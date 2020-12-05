<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\HttpApi;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Violines\RestBundle\HttpApi\AnnotationReaderNotInstalledException;
use Violines\RestBundle\HttpApi\HttpApi;
use Violines\RestBundle\HttpApi\HttpApiReader;
use Violines\RestBundle\HttpApi\MissingHttpApiException;
use Violines\RestBundle\Tests\Stubs\Brownie;
use Violines\RestBundle\Tests\Stubs\Candy;

/**
 * @covers \Violines\RestBundle\HttpApi\HttpApiReader
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

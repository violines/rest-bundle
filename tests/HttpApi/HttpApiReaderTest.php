<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\HttpApi;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Violines\RestBundle\HttpApi\AnnotationReaderNotInstalledException;
use Violines\RestBundle\HttpApi\HttpApi;
use Violines\RestBundle\HttpApi\HttpApiReader;
use Violines\RestBundle\HttpApi\MissingHttpApiException;

/**
 * @covers \Violines\RestBundle\HttpApi\HttpApiReader
 */
class HttpApiReaderTest extends TestCase
{
    public function testShouldReturnHttpApiAnnotation(): void
    {
        $httpApiReader = new HttpApiReader(new AnnotationReader());

        $this->assertInstanceOf(HttpApi::class, $httpApiReader->read(HttpApiDefault::class));
    }

    public function testShouldThrowAnnotationReaderNotInstalledException(): void
    {
        $this->expectException(AnnotationReaderNotInstalledException::class);

        $httpApiReader = new HttpApiReader();

        $httpApiReader->read(AnnotationOnly::class);
    }

    public function testShouldThrowMissingHttpApiException(): void
    {
        $httpApiReader = new HttpApiReader(new AnnotationReader());

        $this->expectException(MissingHttpApiException::class);

        $httpApiReader->read(FakeAnnotation::class);
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

/**
 * @HttpApi
 * @AnyAnnotation
 */
class HttpApiDefault
{
    public int $weight;

    public string $name;
}

/**
 * @AnyAnnotation
 */
class FakeAnnotation
{
    public int $weight;

    public bool $tastesGood;
}

/**
 * @HttpApi
 */
class AnnotationOnly
{
    public int $weight;

    public bool $tastesGood;
}

#[HttpApi(requestInfoSource: 'query_string')]
class HttpApiQueryString
{
    public int $id;

    public string $name;
}

/**
 * @Annotation
 * @Target("CLASS")
 */
class AnyAnnotation
{
}

<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use TerryApiBundle\HttpApi\AnnotationNotFoundException;
use TerryApiBundle\HttpApi\HttpApi;
use TerryApiBundle\HttpApi\HttpApiReader;
use TerryApiBundle\Tests\Stubs\Brownie;
use TerryApiBundle\Tests\Stubs\Candy;

class HttpApiReaderTest extends TestCase
{
    private HTTPApiReader $httpApiReader;

    public function setUp(): void
    {
        parent::setUp();

        $reader = new AnnotationReader();
        $loader = require __DIR__ . "/../../vendor/autoload.php";
        AnnotationRegistry::registerLoader(array($loader, "loadClass"));

        $this->httpApiReader = new HttpApiReader($reader);
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

<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use TerryApiBundle\Annotation\HTTPApi;
use TerryApiBundle\Annotation\HTTPApiReader;
use TerryApiBundle\Exception\AnnotationNotFoundException;
use TerryApiBundle\Tests\Stubs\Brownie;
use TerryApiBundle\Tests\Stubs\Candy;

class HTTPApiReaderTest extends TestCase
{
    private HTTPApiReader $httpApiReader;

    public function setUp(): void
    {
        parent::setUp();

        $reader = new AnnotationReader();
        $loader = require __DIR__ . "/../../vendor/autoload.php";
        AnnotationRegistry::registerLoader(array($loader, "loadClass"));

        $this->httpApiReader = new HTTPApiReader($reader);
    }

    public function testShouldReturnStructAnnotation(): void
    {
        $this->assertInstanceOf(HTTPApi::class, $this->httpApiReader->read(Candy::class));
    }

    public function testShouldThrowAnnotationNotFoundException(): void
    {
        $this->expectException(AnnotationNotFoundException::class);

        $this->httpApiReader->read(Brownie::class);
    }
}

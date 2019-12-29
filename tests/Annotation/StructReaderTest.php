<?php

declare(strict_types=1);

namespace TerryApi\Tests\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use TerryApiBundle\Annotation\Struct;
use TerryApiBundle\Annotation\StructReader;
use TerryApiBundle\Exception\AnnotationNotFoundException;
use TerryApiBundle\Tests\Stubs\BrownieModelStub;
use TerryApiBundle\Tests\Stubs\CandyStructStub;

class StructReaderTest extends TestCase
{
    private StructReader $structReader;

    public function setUp(): void
    {
        parent::setUp();

        $reader = new AnnotationReader();
        $loader = require __DIR__ . "/../../vendor/autoload.php";
        AnnotationRegistry::registerLoader(array($loader, "loadClass"));

        $this->structReader = new StructReader($reader);
    }

    public function testShouldReturnStructAnnotation(): void
    {
        $struct = $this->structReader->read(CandyStructStub::class);

        $this->assertInstanceOf(Struct::class, $struct);
    }

    public function testShouldThrowAnnotationNotFoundException(): void
    {
        $this->expectException(AnnotationNotFoundException::class);

        $this->structReader->read(BrownieModelStub::class);
    }
}

<?php

declare(strict_types=1);

namespace TerryApi\Tests\ArgumentValueResolver;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Serializer;
use TerryApiBundle\Annotation\Struct;
use TerryApiBundle\Annotation\StructReader;
use TerryApiBundle\ArgumentValueResolver\RequestStructResolver;
use TerryApiBundle\Exception\AnnotationNotFoundException;
use TerryApiBundle\Tests\Stubs\CandyStructStub;

class RequestStructResolverTest extends TestCase
{
    /**
     * @Mock
     * @var Serializer
     */
    private \Phake_IMock $serializer;

    /**
     * @Mock
     * @var StructReader
     */
    private \Phake_IMock $structReader;

    /**
     * @Mock
     * @var HttpFoundationRequest
     */
    private \Phake_IMock $request;

    /**
     * @Mock
     * @var ArgumentMetadata
     */
    private \Phake_IMock $argument;

    private RequestStructResolver $requestStructResolver;

    public function setUp(): void
    {
        parent::setUp();

        \Phake::initAnnotations($this);

        $this->requestStructResolver = new RequestStructResolver($this->serializer, $this->structReader);
    }

    /**
     * @dataProvider providerSupportsShouldReturnFalse
     */
    public function testSupportsShouldReturnFalse(?string $type)
    {
        \Phake::when($this->argument)->getType->thenReturn($type);
        \Phake::when($this->structReader)->read->thenThrow(new AnnotationNotFoundException());

        $supports = $this->requestStructResolver->supports($this->request, $this->argument);

        $this->assertFalse($supports);
    }

    public function providerSupportsShouldReturnFalse(): array
    {
        return [
            ['string'],
            [null],
            [CandyStructStub::class],
        ];
    }

    public function testSupportsShouldReturnTrue()
    {
        \Phake::when($this->argument)->getType->thenReturn(CandyStructStub::class);

        $structAnnotation = new Struct();
        $structAnnotation->supports = true;
        \Phake::when($this->structReader)->read->thenReturn($structAnnotation);

        $supports = $this->requestStructResolver->supports($this->request, $this->argument);

        $this->assertTrue($supports);
    }
}

<?php

declare(strict_types=1);

namespace TerryApi\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use TerryApiBundle\Annotation\StructReader;
use TerryApiBundle\EventListener\ResponseTransformListener;
use TerryApiBundle\Tests\Stubs\CandyStructStub;
use TerryApiBundle\ValueObject\HTTPServerDefaults;

class ResponseTransformListenerTest extends TestCase
{
    /**
     * @Mock
     * @var HttpKernel
     */
    private \Phake_IMock $httpKernel;

    /**
     * @Mock
     * @var HttpFoundationRequest
     */
    private \Phake_IMock $request;

    /**
     * @Mock
     * @var SerializerInterface
     */
    private \Phake_IMock $serializer;

    /**
     * @Mock
     * @var StructReader
     */
    private \Phake_IMock $structReader;

    private ResponseTransformListener $responseTransformListener;

    public function setUp(): void
    {
        parent::setUp();

        \Phake::initAnnotations($this);

        $this->request->headers = new HeaderBag([
            'Accept' => 'application/pdf, application/json, application/xml',
            'Content-Type' => 'application/json'
        ]);

        $this->responseTransformListener = new ResponseTransformListener(
            new HTTPServerDefaults(),
            $this->serializer,
            $this->structReader
        );
    }

    /**
     * @dataProvider providerShouldPassControllerResultToSerializer
     */
    public function testShouldPassControllerResultToSerializer($given, string $expected)
    {
        \Phake::when($this->serializer)->serialize->thenReturn($expected);

        $viewEvent = new ViewEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST,
            $given
        );

        $this->responseTransformListener->transform($viewEvent);

        $this->assertEquals($expected, $viewEvent->getResponse()->getContent());
    }

    public function providerShouldPassControllerResultToSerializer()
    {
        return [
            [[new CandyStructStub()], '[]']
        ];
    }

    public function testShouldSkipListener()
    {
        $viewEvent = new ViewEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST,
            null
        );

        $this->responseTransformListener->transform($viewEvent);

        $this->assertNull($viewEvent->getResponse());
    }
}

<?php

declare(strict_types=1);

namespace TerryApi\Tests\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use TerryApiBundle\Annotation\StructReader;
use TerryApiBundle\EventListener\ResponseTransformListener;
use TerryApiBundle\Builder\ResponseBuilder;
use TerryApiBundle\Event\SerializeContextEvent;
use TerryApiBundle\Facade\SerializerFacade;
use TerryApiBundle\Tests\Stubs\GumModelStub;
use TerryApiBundle\Tests\Stubs\OkStructStub;
use TerryApiBundle\ValueObject\HTTPClient;
use TerryApiBundle\ValueObject\HTTPServer;

class ResponseTransformListenerTest extends TestCase
{

    /**
     * @Mock
     * @var EventDispatcherInterface
     */
    private \Phake_IMock $eventDispatcher;

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

    private StructReader $structReader;

    private ResponseTransformListener $responseTransformListener;

    public function setUp(): void
    {
        parent::setUp();

        \Phake::initAnnotations($this);
        \Phake::when($this->request)->getLocale->thenReturn('en_GB');

        $this->request->headers = new HeaderBag([
            'Accept' => 'application/pdf, application/json, application/xml',
            'Content-Type' => 'application/json'
        ]);

        $this->structReader = new StructReader(new AnnotationReader());

        $serializerFacade = new SerializerFacade($this->eventDispatcher, $this->serializer);

        $this->responseTransformListener = new ResponseTransformListener(
            new HTTPServer(),
            new ResponseBuilder(),
            $serializerFacade,
            $this->structReader
        );
    }

    /**
     * @dataProvider providerShouldPassControllerResultToSerializer
     */
    public function testShouldPassControllerResultToSerializer($controllerResult, string $expected)
    {
        \Phake::when($this->serializer)->serialize->thenReturn($expected);
        \Phake::when($this->eventDispatcher)->dispatch->thenReturn(new SerializeContextEvent(
            $controllerResult,
            HTTPClient::fromRequest($this->request, new HTTPServer())
        ));

        $viewEvent = new ViewEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST,
            $controllerResult
        );

        $this->responseTransformListener->transform($viewEvent);

        $this->assertEquals($expected, $viewEvent->getResponse()->getContent());
    }

    public function providerShouldPassControllerResultToSerializer()
    {
        return [
            [[new OkStructStub()], '[{"message": "Everything is fine."}]']
        ];
    }

    /**
     * @dataProvider providerShouldSkipListener
     */
    public function testShouldSkipListener($controllerResult)
    {
        $viewEvent = new ViewEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST,
            $controllerResult
        );

        $this->responseTransformListener->transform($viewEvent);

        $this->assertNull($viewEvent->getResponse());
    }

    public function providerShouldSkipListener()
    {
        return [
            [null],
            [new GumModelStub()]
        ];
    }
}

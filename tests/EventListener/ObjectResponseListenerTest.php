<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use TerryApiBundle\Annotation\HTTPApiReader;
use TerryApiBundle\Builder\ResponseBuilder;
use TerryApiBundle\Event\SerializeEvent;
use TerryApiBundle\EventListener\ObjectResponseListener;
use TerryApiBundle\Facade\SerializerFacade;
use TerryApiBundle\HttpClient\HttpClient;
use TerryApiBundle\HttpClient\HttpClientFactory;
use TerryApiBundle\HttpClient\ServerSettings;
use TerryApiBundle\HttpClient\ServerSettingsFactory;
use TerryApiBundle\Tests\Stubs\Gum;
use TerryApiBundle\Tests\Stubs\Ok;

class ObjectResponseListenerTest extends TestCase
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

    private ObjectResponseListener $listener;

    public function setUp(): void
    {
        parent::setUp();

        \Phake::initAnnotations($this);
        \Phake::when($this->request)->getLocale->thenReturn('en_GB');

        $this->request->headers = new HeaderBag([
            'Accept' => 'application/pdf, application/json, application/xml',
            'Content-Type' => 'application/json'
        ]);

        $httpApiReader = new HTTPApiReader(new AnnotationReader());

        $serializerFacade = new SerializerFacade($this->eventDispatcher, $this->serializer);

        $this->listener = new ObjectResponseListener(
            new HttpClientFactory(new ServerSettingsFactory([])),
            new ResponseBuilder(),
            $serializerFacade,
            $httpApiReader
        );
    }

    /**
     * @dataProvider providerShouldPassControllerResultToSerializer
     */
    public function testShouldPassControllerResultToSerializer($controllerResult, string $expected)
    {
        \Phake::when($this->serializer)->serialize($controllerResult, 'json', [])->thenReturn($expected);
        \Phake::when($this->eventDispatcher)->dispatch->thenReturn(new SerializeEvent(
            $controllerResult,
            HttpClient::new($this->request, ServerSettings::fromDefaults())
        ));

        $viewEvent = new ViewEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST,
            $controllerResult
        );

        $this->listener->transform($viewEvent);

        $this->assertEquals($expected, $viewEvent->getResponse()->getContent());
    }

    public function providerShouldPassControllerResultToSerializer()
    {
        return [
            [[new Ok()], '[{"message": "Everything is fine."}]']
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

        $this->listener->transform($viewEvent);

        $this->assertNull($viewEvent->getResponse());
    }

    public function providerShouldSkipListener()
    {
        return [
            [null],
            [new Gum()],
            ['key' => 'value'],
            [['key' => 'value']]
        ];
    }
}

<?php

declare(strict_types=1);

namespace TerryApi\Tests\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use TerryApiBundle\Annotation\HTTPApiReader;
use TerryApiBundle\Builder\ResponseBuilder;
use TerryApiBundle\Event\SerializeEvent;
use TerryApiBundle\EventListener\HTTPErrorListener;
use TerryApiBundle\Exception\AnnotationNotFoundException;
use TerryApiBundle\Facade\SerializerFacade;
use TerryApiBundle\HttpClient\HttpClient;
use TerryApiBundle\HttpClient\HttpClientFactory;
use TerryApiBundle\HttpClient\ServerSettings;
use TerryApiBundle\HttpClient\ServerSettingsFactory;
use TerryApiBundle\Tests\Stubs\Error;
use TerryApiBundle\Tests\Stubs\Gum;
use TerryApiBundle\Tests\Stubs\HTTPErrorException;

class HTTPErrorListenerTest extends TestCase
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

    private HTTPErrorListener $httpErrorListener;

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

        $this->httpErrorListener = new HTTPErrorListener(
            new HttpClientFactory(new ServerSettingsFactory([])),
            new ResponseBuilder(),
            $serializerFacade,
            $httpApiReader
        );
    }

    public function testShouldCreateCandyStructStubJson()
    {
        $expectedJson = '{"message": "Test 400"}';
        $exception = new HTTPErrorException();
        $exception->setContent(new Error("Test 400"));

        \Phake::when($this->eventDispatcher)->dispatch->thenReturn(new SerializeEvent(
            $exception->getContent(),
            HttpClient::new($this->request, ServerSettings::fromDefaults())
        ));

        \Phake::when($this->serializer)
            ->serialize($exception->getContent(), 'json', [])
            ->thenReturn($expectedJson);

        $exceptionEvent = new ExceptionEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->httpErrorListener->handle($exceptionEvent);

        $response = $exceptionEvent->getResponse();

        $this->assertJsonStringEqualsJsonString($expectedJson, $response->getContent());
        $this->assertEquals($exception->getHTTPStatusCode(), $response->getStatusCode());
    }

    public function testShouldSkipListener()
    {
        $exception = new \Exception();

        $exceptionEvent = new ExceptionEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->httpErrorListener->handle($exceptionEvent);

        $this->assertNull($exceptionEvent->getResponse());
    }

    public function testShouldThrowAnnotationNotFoundException()
    {
        $this->expectException(AnnotationNotFoundException::class);

        $exception = new HTTPErrorException();
        $exception->setContent(new Gum());

        $exceptionEvent = new ExceptionEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->httpErrorListener->handle($exceptionEvent);
    }
}

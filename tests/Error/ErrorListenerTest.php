<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Error;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use TerryApiBundle\Error\ErrorListener;
use TerryApiBundle\Response\ResponseBuilder;
use TerryApiBundle\Serialize\SerializeEvent;
use TerryApiBundle\HttpApi\AnnotationNotFoundException;
use TerryApiBundle\HttpApi\HttpApiReader;
use TerryApiBundle\Negotiation\ContentNegotiator;
use TerryApiBundle\Serialize\FormatMapper;
use TerryApiBundle\Serialize\Serializer;
use TerryApiBundle\Tests\Error\ErrorException;
use TerryApiBundle\Tests\Stubs\Config;
use TerryApiBundle\Tests\Stubs\Error;
use TerryApiBundle\Tests\Stubs\Gum;

class ErrorListenerTest extends TestCase
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

    private ErrorListener $errorListener;

    public function setUp(): void
    {
        parent::setUp();
        \Phake::initAnnotations($this);

        $this->request->headers = new HeaderBag([
            'Accept' => 'application/pdf, application/json, application/xml',
            'Content-Type' => 'application/json'
        ]);

        $this->errorListener = new ErrorListener(
            new HttpApiReader(new AnnotationReader()),
            new ContentNegotiator(Config::SERIALIZE_FORMATS, Config::SERIALIZE_FORMAT_DEFAULT),
            new ResponseBuilder(),
            new Serializer($this->eventDispatcher, $this->serializer, new FormatMapper(Config::SERIALIZE_FORMATS))
        );
    }

    public function testShouldCreateCandyStructStubJson(): void
    {
        $expectedJson = '{"message": "Test 400"}';
        $exception = new ErrorException();
        $exception->setContent(new Error("Test 400"));

        \Phake::when($this->eventDispatcher)->dispatch->thenReturn(new SerializeEvent(
            $exception->getContent(),
            'json'
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

        $this->errorListener->handle($exceptionEvent);

        $response = $exceptionEvent->getResponse();

        $this->assertJsonStringEqualsJsonString($expectedJson, $response->getContent());
        $this->assertEquals($exception->getStatusCode(), $response->getStatusCode());
    }

    public function testShouldSkipListener(): void
    {
        $exception = new \Exception();

        $exceptionEvent = new ExceptionEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->errorListener->handle($exceptionEvent);

        $this->assertNull($exceptionEvent->getResponse());
    }

    public function testShouldThrowAnnotationNotFoundException(): void
    {
        $this->expectException(AnnotationNotFoundException::class);

        $exception = new ErrorException();
        $exception->setContent(new Gum());

        $exceptionEvent = new ExceptionEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->errorListener->handle($exceptionEvent);
    }
}

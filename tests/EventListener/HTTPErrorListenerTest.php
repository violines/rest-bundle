<?php

declare(strict_types=1);

namespace TerryApi\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use TerryApiBundle\Annotation\StructReader;
use TerryApiBundle\EventListener\HTTPErrorListener;
use TerryApiBundle\Tests\Stubs\HTTPErrorExceptionStub;
use TerryApiBundle\ValueObject\HTTPServer;

class HTTPErrorListenerTest extends TestCase
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

    private HTTPErrorListener $httpErrorListener;

    public function setUp(): void
    {
        parent::setUp();

        \Phake::initAnnotations($this);

        $this->request->headers = new HeaderBag([
            'Accept' => 'application/pdf, application/json, application/xml',
            'Content-Type' => 'application/json'
        ]);

        $this->httpErrorListener = new HTTPErrorListener(
            new HTTPServer(),
            $this->serializer,
            $this->structReader
        );
    }

    public function testShouldCreateCandyStructStubJson()
    {
        $expectedJson = '{"weight": 100,"name": "Bonbon","tastesGood": true}';
        $exception = new HTTPErrorExceptionStub();

        \Phake::when($this->serializer)
            ->serialize($exception->getStruct(), 'json')
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
}

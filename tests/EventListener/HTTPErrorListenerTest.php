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
use TerryApiBundle\Annotation\StructReader;
use TerryApiBundle\EventListener\HTTPErrorListener;
use TerryApiBundle\Exception\AnnotationNotFoundException;
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

    private StructReader $structReader;

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

        $this->structReader = new StructReader(new AnnotationReader());

        $this->httpErrorListener = new HTTPErrorListener(
            new HTTPServer(),
            $this->serializer,
            $this->structReader
        );
    }

    public function testShouldCreateCandyStructStubJson()
    {
        $expectedJson = '{"message": "Test 400"}';
        $exception = new HTTPErrorExceptionStub();
        $exception->setStructToStruct();

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

    public function testShouldThrowAnnotationNotFoundException()
    {
        $this->expectException(AnnotationNotFoundException::class);

        $exception = new HTTPErrorExceptionStub();
        $exception->setStructToNonStructObject();

        $exceptionEvent = new ExceptionEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->httpErrorListener->handle($exceptionEvent);
    }
}

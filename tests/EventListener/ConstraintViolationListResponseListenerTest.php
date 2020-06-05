<?php

declare(strict_types=1);

namespace TerryApi\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use TerryApiBundle\Builder\ResponseBuilder;
use TerryApiBundle\Event\SerializeEvent;
use TerryApiBundle\EventListener\ConstraintViolationListResponseListener;
use TerryApiBundle\Exception\ValidationException;
use TerryApiBundle\Facade\SerializerFacade;
use TerryApiBundle\Tests\Stubs\ConstraintViolationList;
use TerryApiBundle\ValueObject\HTTPClient;
use TerryApiBundle\ValueObject\HTTPServer;

class ConstraintViolationListResponseListenerTest extends TestCase
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

    private ConstraintViolationListResponseListener $listener;

    public function setUp(): void
    {
        parent::setUp();

        \Phake::initAnnotations($this);
        \Phake::when($this->request)->getLocale->thenReturn('en_GB');

        $this->request->headers = new HeaderBag([
            'Accept' => 'application/pdf, application/json, application/xml',
            'Content-Type' => 'application/json'
        ]);

        $serializerFacade = new SerializerFacade($this->eventDispatcher, $this->serializer);

        $this->listener = new ConstraintViolationListResponseListener(
            new HTTPServer(),
            new ResponseBuilder(),
            $serializerFacade
        );
    }

    public function testShouldCreateViolationResponse()
    {
        $exception = ValidationException::create(new ConstraintViolationList());
        \Phake::when($this->serializer)->serialize->thenReturn('string');
        \Phake::when($this->eventDispatcher)->dispatch->thenReturn(new SerializeEvent(
            $exception,
            HTTPClient::fromRequest($this->request, new HTTPServer())
        ));

        $exceptionEvent = new ExceptionEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->listener->handle($exceptionEvent);

        \Phake::verify($this->serializer)->serialize(\Phake::capture($data), 'json', []);

        $this->assertInstanceOf(ConstraintViolationListInterface::class, $data);
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

        $this->listener->handle($exceptionEvent);

        $this->assertNull($exceptionEvent->getResponse());
    }
}

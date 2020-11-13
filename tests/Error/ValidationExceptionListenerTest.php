<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Error;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use TerryApiBundle\Error\ValidationException;
use TerryApiBundle\Error\ValidationExceptionListener;
use TerryApiBundle\Serialize\SerializeEvent;
use TerryApiBundle\Response\ResponseBuilder;
use TerryApiBundle\Negotiation\ContentNegotiator;
use TerryApiBundle\Serialize\FormatMapper;
use TerryApiBundle\Serialize\Serializer;
use TerryApiBundle\Tests\Stubs\Config;

/**
 * @covers \TerryApiBundle\Error\ValidationExceptionListener
 * @uses \TerryApiBundle\Error\ValidationException
 * @uses \TerryApiBundle\Serialize\SerializeEvent
 */
class ValidationExceptionListenerTest extends TestCase
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

    private ValidationExceptionListener $listener;

    public function setUp(): void
    {
        parent::setUp();
        \Phake::initAnnotations($this);

        $this->request->headers = new HeaderBag([
            'Accept' => 'application/pdf, application/json, application/xml',
            'Content-Type' => 'application/json'
        ]);

        $this->listener = new ValidationExceptionListener(
            new ContentNegotiator(Config::SERIALIZE_FORMATS, Config::SERIALIZE_FORMAT_DEFAULT),
            new ResponseBuilder(),
            new Serializer($this->eventDispatcher, $this->serializer, new FormatMapper(Config::SERIALIZE_FORMATS))
        );
    }

    public function testShouldCreateViolationResponse(): void
    {
        $exception = ValidationException::fromViolationList(new ConstraintViolationList());
        \Phake::when($this->serializer)->serialize->thenReturn('string');
        \Phake::when($this->eventDispatcher)->dispatch->thenReturn(new SerializeEvent(
            $exception,
            'json'
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

    public function testShouldSkipListener(): void
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

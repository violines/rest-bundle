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
use TerryApiBundle\Builder\ResponseBuilder;
use TerryApiBundle\Error\ValidationExceptionListener;
use TerryApiBundle\Event\SerializeEvent;
use TerryApiBundle\Error\ValidationException;
use TerryApiBundle\Facade\SerializerFacade;
use TerryApiBundle\HttpClient\HttpClient;
use TerryApiBundle\HttpClient\HttpClientFactory;
use TerryApiBundle\HttpClient\ServerSettings;
use TerryApiBundle\HttpClient\ServerSettingsFactory;

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
        \Phake::when($this->request)->getLocale->thenReturn('en_GB');

        $this->request->headers = new HeaderBag([
            'Accept' => 'application/pdf, application/json, application/xml',
            'Content-Type' => 'application/json'
        ]);

        $serializerFacade = new SerializerFacade($this->eventDispatcher, $this->serializer);

        $this->listener = new ValidationExceptionListener(
            new HttpClientFactory(new ServerSettingsFactory([])),
            new ResponseBuilder(),
            $serializerFacade
        );
    }

    public function testShouldCreateViolationResponse()
    {
        $exception = ValidationException::fromViolationList(new ConstraintViolationList());
        \Phake::when($this->serializer)->serialize->thenReturn('string');
        \Phake::when($this->eventDispatcher)->dispatch->thenReturn(new SerializeEvent(
            $exception,
            HttpClient::new($this->request, ServerSettings::fromDefaults())
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

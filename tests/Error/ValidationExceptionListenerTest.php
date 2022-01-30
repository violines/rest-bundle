<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Error;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Violines\RestBundle\Error\ValidationException;
use Violines\RestBundle\Error\ValidationExceptionListener;
use Violines\RestBundle\Negotiation\ContentNegotiator;
use Violines\RestBundle\Response\ResponseBuilder;
use Violines\RestBundle\Serialize\FormatMapper;
use Violines\RestBundle\Serialize\Serializer;
use Violines\RestBundle\Tests\Fake\ConstraintViolationFake;
use Violines\RestBundle\Tests\Fake\ConstraintViolationListFake;
use Violines\RestBundle\Tests\Fake\SymfonyEventDispatcherFake;
use Violines\RestBundle\Tests\Fake\SymfonySerializerFake;
use Violines\RestBundle\Tests\Stub\Config;

/**
 * @covers \Violines\RestBundle\Error\ValidationExceptionListener
 *
 * @uses \Violines\RestBundle\Error\ValidationException
 * @uses \Violines\RestBundle\Serialize\SerializeEvent
 */
class ValidationExceptionListenerTest extends TestCase
{
    use ProphecyTrait;

    private ValidationExceptionListener $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->listener = new ValidationExceptionListener(
            new ContentNegotiator(Config::SERIALIZE_FORMATS, Config::SERIALIZE_FORMAT_DEFAULT),
            new ResponseBuilder(),
            new Serializer(new SymfonyEventDispatcherFake(), new SymfonySerializerFake(), new FormatMapper(Config::SERIALIZE_FORMATS))
        );
    }

    public function testShouldCreateViolationResponse(): void
    {
        $expectedEncodedError = '[{"message":"message","messageTemplate":"message_tpl","parameters":[],"plural":null,"root":null,"propertyPath":"path","invalidValue":null,"code":null}]';

        $violationList = new ConstraintViolationListFake();
        $violationList->add(new ConstraintViolationFake());
        $exception = ValidationException::fromViolationList($violationList);

        $exceptionEvent = new ExceptionEvent(
            $this->prophesize(HttpKernel::class)->reveal(),
            $this->createMockRequestWithHeaders()->reveal(),
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->listener->handle($exceptionEvent);

        $this->assertSame($expectedEncodedError, $exceptionEvent->getResponse()->getContent());
    }

    public function testShouldSkipListener(): void
    {
        $exception = new \Exception();

        $exceptionEvent = new ExceptionEvent(
            $this->prophesize(HttpKernel::class)->reveal(),
            $this->createMockRequestWithHeaders()->reveal(),
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->listener->handle($exceptionEvent);

        $this->assertNull($exceptionEvent->getResponse());
    }

    private function createMockRequestWithHeaders()
    {
        $request = $this->prophesize(HttpFoundationRequest::class);

        $request->headers = new HeaderBag([
            'Accept' => 'application/pdf, application/json, application/xml',
            'Content-Type' => 'application/json',
        ]);

        return $request;
    }
}

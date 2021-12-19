<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Error;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Violines\RestBundle\Error\NotAcceptableListener;
use Violines\RestBundle\Negotiation\MimeType;
use Violines\RestBundle\Negotiation\NotNegotiableException;
use Violines\RestBundle\Response\ResponseBuilder;
use Violines\RestBundle\Serialize\FormatException;

/**
 * @covers \Violines\RestBundle\Error\NotAcceptableListener
 *
 * @uses \Violines\RestBundle\Negotiation\MimeType
 * @uses \Violines\RestBundle\Negotiation\NotNegotiableException
 * @uses \Violines\RestBundle\Serialize\FormatException
 */
class NotAcceptableListenerTest extends TestCase
{
    use ProphecyTrait;

    private NotAcceptableListener $notAcceptableListener;

    /**
     * @dataProvider providerShouldReturnNotAcceptableAndLog
     */
    public function testShouldReturnNotAcceptableAndLog(\Exception $givenException, string $expectedLogMessage): void
    {
        $logger = $this->prophesize(LoggerInterface::class);
        $logger->log('debug', $expectedLogMessage)->shouldBeCalled();
        $notAcceptableListener = new NotAcceptableListener(new ResponseBuilder(), $logger->reveal());

        $exceptionEvent = new ExceptionEvent($this->prophesize(HttpKernel::class)->reveal(), $this->prophesize(HttpFoundationRequest::class)->reveal(), HttpKernelInterface::MASTER_REQUEST, $givenException);

        $notAcceptableListener->handle($exceptionEvent);
        $response = $exceptionEvent->getResponse();
        $this->assertEquals(Response::HTTP_NOT_ACCEPTABLE, $response->getStatusCode());
    }

    public function providerShouldReturnNotAcceptableAndLog(): array
    {
        return [
            [
                FormatException::notConfigured(MimeType::fromString('text/html')),
                'MimeType text/html was not configured for any Format. Check configuration under serialize > formats',
            ],
            [
                NotNegotiableException::notConfigured('application/atom+xml'),
                'None of the accepted mimetypes application/atom+xml are configured for any Format. Check configuration under serialize > formats',
            ],
        ];
    }

    public function testShouldReturnNotAcceptableAndNullLog(): void
    {
        $exceptionEvent = new ExceptionEvent($this->prophesize(HttpKernel::class)->reveal(), $this->prophesize(HttpFoundationRequest::class)->reveal(), HttpKernelInterface::MASTER_REQUEST, NotNegotiableException::notConfigured('application/atom+xml'));

        $listenerWithNullLogger = new NotAcceptableListener(new ResponseBuilder(), null);

        $listenerWithNullLogger->handle($exceptionEvent);

        $this->assertEquals(Response::HTTP_NOT_ACCEPTABLE, $exceptionEvent->getResponse()->getStatusCode());
    }

    public function testShouldSkipListener(): void
    {
        $exception = new \Exception();

        $exceptionEvent = new ExceptionEvent(
            $this->prophesize(HttpKernel::class)->reveal(),
            $this->prophesize(HttpFoundationRequest::class)->reveal(),
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $listenerWithNullLogger = new NotAcceptableListener(new ResponseBuilder(), null);
        $listenerWithNullLogger->handle($exceptionEvent);

        $this->assertNull($exceptionEvent->getResponse());
    }
}

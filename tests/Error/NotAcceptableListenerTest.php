<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Error;

use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use TerryApiBundle\Error\NotAcceptableListener;
use TerryApiBundle\Negotiation\MimeType;
use TerryApiBundle\Negotiation\NotNegotiableException;
use TerryApiBundle\Response\ResponseBuilder;
use TerryApiBundle\Serialize\FormatException;

class NotAcceptableListenerTest extends TestCase
{
    /**
     * @Mock
     * @var LoggerInterface
     */
    private \Phake_IMock $logger;

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

    private NotAcceptableListener $notAcceptableListener;

    public function setUp(): void
    {
        parent::setUp();
        \Phake::initAnnotations($this);
        $this->notAcceptableListener = new NotAcceptableListener(new ResponseBuilder(), $this->logger);
    }

    public function testShouldReturnNotAcceptableByFormatException()
    {
        $exception = FormatException::notConfigured(MimeType::fromString('text/html'));

        $exceptionEvent = new ExceptionEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->notAcceptableListener->handle($exceptionEvent);

        \Phake::verify($this->logger)->log(\Phake::capture($logLevel), \Phake::capture($exceptionMessage));
        $this->assertEquals('info', $logLevel);
        $this->assertEquals('MimeType text/html was not configured for any Format. Check configuration under serialize > formats', $exceptionMessage);
        
        $response = $exceptionEvent->getResponse();
        $this->assertEquals(Response::HTTP_NOT_ACCEPTABLE, $response->getStatusCode());
    }

    public function testShouldReturnNotAcceptableByNotNegotiableException()
    {
        $exception = NotNegotiableException::notConfigured('application/atom+xml');

        $exceptionEvent = new ExceptionEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->notAcceptableListener->handle($exceptionEvent);

        \Phake::verify($this->logger)->log(\Phake::capture($logLevel), \Phake::capture($exceptionMessage));
        $this->assertEquals('info', $logLevel);
        $this->assertEquals('None of the accepted mimetypes application/atom+xml are configured for any Format. Check configuration under serialize > formats', $exceptionMessage);
        
        $response = $exceptionEvent->getResponse();
        $this->assertEquals(Response::HTTP_NOT_ACCEPTABLE, $response->getStatusCode());
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

        $this->notAcceptableListener->handle($exceptionEvent);

        $this->assertNull($exceptionEvent->getResponse());
    }
}

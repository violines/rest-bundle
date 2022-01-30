<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Error;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Violines\RestBundle\Error\ErrorInterface;
use Violines\RestBundle\Error\ErrorListener;
use Violines\RestBundle\HttpApi\HttpApi;
use Violines\RestBundle\HttpApi\HttpApiReader;
use Violines\RestBundle\HttpApi\MissingHttpApiException;
use Violines\RestBundle\Negotiation\ContentNegotiator;
use Violines\RestBundle\Response\ErrorResponseResolver;
use Violines\RestBundle\Response\ResponseBuilder;
use Violines\RestBundle\Serialize\FormatMapper;
use Violines\RestBundle\Serialize\Serializer;
use Violines\RestBundle\Tests\Fake\SymfonyEventDispatcherFake;
use Violines\RestBundle\Tests\Fake\SymfonySerializerFake;
use Violines\RestBundle\Tests\Stub\Config;

/**
 * @covers \Violines\RestBundle\Error\ErrorListener
 * @covers \Violines\RestBundle\Response\ErrorResponseResolver
 *
 * @uses \Violines\RestBundle\Serialize\SerializeEvent
 */
class ErrorListenerTest extends TestCase
{
    use ProphecyTrait;

    private ErrorListener $errorListener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->errorListener = new ErrorListener(
            new HttpApiReader(new AnnotationReader()),
            new ErrorResponseResolver(
                new ContentNegotiator(Config::SERIALIZE_FORMATS, Config::SERIALIZE_FORMAT_DEFAULT),
                new ResponseBuilder(),
                new Serializer(new SymfonyEventDispatcherFake(), new SymfonySerializerFake(), new FormatMapper(Config::SERIALIZE_FORMATS))
            )
        );
    }

    public function testShouldCreateErrorJson(): void
    {
        $expectedJson = '{"message": "Test 400"}';
        $exception = new ErrorException();
        $exception->setContent(new Error('Test 400'));

        $exceptionEvent = new ExceptionEvent(
            $this->prophesize(HttpKernel::class)->reveal(),
            $this->createMockRequestWithHeaders()->reveal(),
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
            $this->prophesize(HttpKernel::class)->reveal(),
            $this->createMockRequestWithHeaders()->reveal(),
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->errorListener->handle($exceptionEvent);

        $this->assertNull($exceptionEvent->getResponse());
    }

    public function testShouldThrowMissingHttpApiException(): void
    {
        $this->expectException(MissingHttpApiException::class);

        $exception = new ErrorException();
        $exception->setContent(new Gum());

        $exceptionEvent = new ExceptionEvent(
            $this->prophesize(HttpKernel::class)->reveal(),
            $this->createMockRequestWithHeaders()->reveal(),
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $this->errorListener->handle($exceptionEvent);
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

class ErrorException extends \LogicException implements \Throwable, ErrorInterface
{
    private $content;

    public function getContent(): object
    {
        return $this->content;
    }

    public function getStatusCode(): int
    {
        return 400;
    }

    public function setContent(object $content): void
    {
        $this->content = $content;
    }
}

/**
 * @HttpApi
 */
class Error
{
    public $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}

class Gum
{
    public int $weight;

    public bool $tastesGood;
}

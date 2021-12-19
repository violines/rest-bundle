<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Response;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Violines\RestBundle\HttpApi\HttpApi;
use Violines\RestBundle\HttpApi\HttpApiReader;
use Violines\RestBundle\Negotiation\ContentNegotiator;
use Violines\RestBundle\Response\ResponseBuilder;
use Violines\RestBundle\Response\ResponseListener;
use Violines\RestBundle\Response\SuccessResponseResolver;
use Violines\RestBundle\Serialize\FormatMapper;
use Violines\RestBundle\Serialize\Serializer;
use Violines\RestBundle\Tests\Fake\SymfonyEventDispatcherFake;
use Violines\RestBundle\Tests\Fake\SymfonySerializerFake;
use Violines\RestBundle\Tests\Stub\Config;

/**
 * @covers \Violines\RestBundle\Response\ResponseListener
 * @covers \Violines\RestBundle\Response\SuccessResponseResolver
 *
 * @uses \Violines\RestBundle\Serialize\SerializeEvent
 */
class ResponseListenerTest extends TestCase
{
    use ProphecyTrait;

    private ResponseListener $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->listener = new ResponseListener(
            new HttpApiReader(new AnnotationReader()),
            new SuccessResponseResolver(
                new ContentNegotiator(Config::SERIALIZE_FORMATS, Config::SERIALIZE_FORMAT_DEFAULT),
                new ResponseBuilder(),
                new Serializer(new SymfonyEventDispatcherFake(), new SymfonySerializerFake(), new FormatMapper(Config::SERIALIZE_FORMATS))
            )
        );
    }

    /**
     * @dataProvider providerShouldPassControllerResultToSerializer
     */
    public function testShouldPassControllerResultToSerializer($controllerResult, string $expected): void
    {
        $viewEvent = new ViewEvent(
            $this->prophesize(HttpKernel::class)->reveal(),
            $this->createMockRequestWithHeaders()->reveal(),
            HttpKernelInterface::MASTER_REQUEST,
            $controllerResult
        );

        $this->listener->transform($viewEvent);

        $this->assertEquals($expected, $viewEvent->getResponse()->getContent());
    }

    public function providerShouldPassControllerResultToSerializer(): array
    {
        return [
            [[new Ok()], '[{"message":"Everything is fine."}]'],
        ];
    }

    /**
     * @dataProvider providerShouldSkipListener
     */
    public function testShouldSkipListener($controllerResult): void
    {
        $viewEvent = new ViewEvent(
            $this->prophesize(HttpKernel::class)->reveal(),
            $this->createMockRequestWithHeaders()->reveal(),
            HttpKernelInterface::MASTER_REQUEST,
            $controllerResult
        );

        $this->listener->transform($viewEvent);

        $this->assertNull($viewEvent->getResponse());
    }

    public function providerShouldSkipListener(): array
    {
        return [
            [null],
            [new Gum()],
            ['key' => 'value'],
            [['key' => 'value']],
        ];
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

/**
 * @HttpApi
 */
class Ok
{
    public $message = 'Everything is fine.';

    public static function create(): self
    {
        return new self();
    }
}

class Gum
{
    public int $weight;

    public bool $tastesGood;
}

<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Response;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Violines\RestBundle\HttpApi\HttpApiReader;
use Violines\RestBundle\Negotiation\ContentNegotiator;
use Violines\RestBundle\Response\ResponseBuilder;
use Violines\RestBundle\Response\ResponseListener;
use Violines\RestBundle\Serialize\FormatMapper;
use Violines\RestBundle\Serialize\SerializeEvent;
use Violines\RestBundle\Serialize\Serializer;
use Violines\RestBundle\Tests\Stubs\Config;
use Violines\RestBundle\Tests\Stubs\Gum;
use Violines\RestBundle\Tests\Stubs\Ok;

/**
 * @covers \Violines\RestBundle\Response\ResponseListener
 *
 * @uses \Violines\RestBundle\Serialize\SerializeEvent
 */
class ResponseListenerTest extends TestCase
{
    /**
     * @Mock
     *
     * @var EventDispatcherInterface
     */
    private \Phake_IMock $eventDispatcher;

    /**
     * @Mock
     *
     * @var HttpKernel
     */
    private \Phake_IMock $httpKernel;

    /**
     * @Mock
     *
     * @var HttpFoundationRequest
     */
    private \Phake_IMock $request;

    /**
     * @Mock
     *
     * @var SerializerInterface
     */
    private \Phake_IMock $serializer;

    private ResponseListener $listener;

    protected function setUp(): void
    {
        parent::setUp();
        \Phake::initAnnotations($this);

        $this->request->headers = new HeaderBag([
            'Accept' => 'application/pdf, application/json, application/xml',
            'Content-Type' => 'application/json'
        ]);

        $this->listener = new ResponseListener(
            new HttpApiReader(new AnnotationReader()),
            new ContentNegotiator(Config::SERIALIZE_FORMATS, Config::SERIALIZE_FORMAT_DEFAULT),
            new ResponseBuilder(),
            new Serializer($this->eventDispatcher, $this->serializer, new FormatMapper(Config::SERIALIZE_FORMATS))
        );
    }

    /**
     * @dataProvider providerShouldPassControllerResultToSerializer
     */
    public function testShouldPassControllerResultToSerializer($controllerResult, string $expected): void
    {
        \Phake::when($this->serializer)->serialize($controllerResult, 'json', [])->thenReturn($expected);
        \Phake::when($this->eventDispatcher)->dispatch->thenReturn(new SerializeEvent($controllerResult, 'json'));

        $viewEvent = new ViewEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST,
            $controllerResult
        );

        $this->listener->transform($viewEvent);

        $this->assertEquals($expected, $viewEvent->getResponse()->getContent());
    }

    public function providerShouldPassControllerResultToSerializer(): array
    {
        return [
            [[new Ok()], '[{"message": "Everything is fine."}]']
        ];
    }

    /**
     * @dataProvider providerShouldSkipListener
     */
    public function testShouldSkipListener($controllerResult): void
    {
        $viewEvent = new ViewEvent(
            $this->httpKernel,
            $this->request,
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
            [['key' => 'value']]
        ];
    }
}

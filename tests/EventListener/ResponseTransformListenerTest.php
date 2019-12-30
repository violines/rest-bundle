<?php

declare(strict_types=1);

namespace TerryApi\Tests\ResponseTransformListener;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use TerryApiBundle\EventListener\ResponseTransformListener;

class ResponseTransformListenerTest extends TestCase
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

    private ResponseTransformListener $responseTransformListener;

    public function setUp(): void
    {
        parent::setUp();

        \Phake::initAnnotations($this);

        $this->responseTransformListener = new ResponseTransformListener($this->serializer);
    }

    /**
     * @dataProvider providerShouldPassControllerResultToSerializer
     */
    public function testShouldPassControllerResultToSerializer($given, string $expected)
    {
        \Phake::when($this->serializer)->serialize->thenReturn($expected);

        $viewEvent = new ViewEvent(
            $this->httpKernel,
            $this->request,
            HttpKernelInterface::MASTER_REQUEST,
            $given
        );

        $this->responseTransformListener->transform($viewEvent);

        $this->assertEquals($expected, $viewEvent->getResponse()->getContent());
    }

    public function providerShouldPassControllerResultToSerializer()
    {
        return [
            [[], '[]'],
            [null, ''],
        ];
    }
}

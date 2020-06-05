<?php

declare(strict_types=1);

namespace TerryApi\Tests\Builder;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use TerryApiBundle\Builder\ResponseBuilder;
use TerryApiBundle\ValueObject\HTTPClient;
use TerryApiBundle\ValueObject\HTTPServer;

class ResponseBuilderTest extends TestCase
{
    private const FORMAT_SERIALIZER_MAP = [
        'application/json' => 'json',
        'application/xml' => 'xml'
    ];

    private ResponseBuilder $responseBuilder;

    private HTTPServer $httpServer;

    /**
     * @Mock
     * @var HttpFoundationRequest
     */
    private \Phake_IMock $request;

    public function setUp(): void
    {
        parent::setUp();

        $this->responseBuilder = new ResponseBuilder();

        \Phake::initAnnotations($this);
        \Phake::when($this->request)->getLocale->thenReturn('en_GB');
        $this->request->headers = new HeaderBag([
            'Accept' => 'application/json, plain/html',
            'Content-Type' => 'application/json'
        ]);

        $this->httpServer = new HTTPServer('', self::FORMAT_SERIALIZER_MAP);
    }

    public function testShouldReturnEmptyResponse()
    {
        $this->assertInstanceOf(Response::class, $this->responseBuilder->getResponse());
    }

    public function testShouldReturnResponseWithContent()
    {
        $content = '{"text": "i am a string"}';

        $response = $this->responseBuilder
            ->setClient(HTTPClient::fromRequest($this->request, $this->httpServer))
            ->setContent($content)
            ->getResponse();

        $this->assertEquals($content, $response->getContent());
    }

    /**
     * @dataProvider providerShouldResponseWithCustomStatusCode
     */
    public function testShouldResponseWithCustomStatusCode(int $status)
    {
        $response = $this->responseBuilder->setStatus($status)->getResponse();

        $this->assertEquals($status, $response->getStatusCode());
    }

    public function providerShouldResponseWithCustomStatusCode()
    {
        return [
            [Response::HTTP_CREATED]
        ];
    }

    public function testShouldResponseWithHeaders()
    {
        $response = $this->responseBuilder
            ->setClient(HTTPClient::fromRequest($this->request, $this->httpServer))
            ->getResponse();

        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }
}

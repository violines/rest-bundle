<?php

declare(strict_types=1);

namespace TerryApi\Tests\Builder;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use TerryApiBundle\Builder\ResponseBuilder;
use TerryApiBundle\HttpClient\HttpClient;
use TerryApiBundle\HttpClient\ServerSettings;

class ResponseBuilderTest extends TestCase
{
    private const FORMAT_SERIALIZER_MAP = [
        'application/json' => 'json',
        'application/xml' => 'xml',
        'html' => 'xml'
    ];

    private ResponseBuilder $responseBuilder;

    private ServerSettings $httpServer;

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

        $this->httpServer = new ServerSettings('', self::FORMAT_SERIALIZER_MAP);
    }

    public function testShouldReturnEmptyResponse()
    {
        $this->assertInstanceOf(Response::class, $this->responseBuilder->getResponse());
    }

    public function testShouldReturnResponseWithContent()
    {
        $content = '{"text": "i am a string"}';

        $response = $this->responseBuilder
            ->setContent($content)
            ->getResponse();

        $this->assertEquals($content, $response->getContent());
        $this->assertEquals(null, $response->headers->get('content-type'));
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testShouldResponseWithCustomStatusCode()
    {
        $response = $this->responseBuilder->setStatus(Response::HTTP_CREATED)->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testShouldResponseWithHeaders()
    {
        $response = $this->responseBuilder
            ->setClient(HttpClient::fromRequest($this->request, $this->httpServer))
            ->getResponse();

        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }

    /**
     * @dataProvider providerShouldResponseWithProblem
     */
    public function testShouldResponseWithProblem(string $accept, int $status, string $expected)
    {
        $this->request->headers->set('Accept', $accept);

        $response = $this->responseBuilder
            ->setClient(HttpClient::fromRequest($this->request, $this->httpServer))
            ->setStatus($status)
            ->getResponse();

        $this->assertEquals($expected, $response->headers->get('content-type'));
    }

    public function providerShouldResponseWithProblem()
    {
        return [
            ['application/json', 400, 'application/problem+json'],
            ['html', 403, 'problem+html'],
            ['application/json', 500, 'application/json']
        ];
    }
}

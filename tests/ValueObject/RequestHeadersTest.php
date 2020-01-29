<?php

declare(strict_types=1);

namespace TerryApi\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use TerryApiBundle\Exception\RequestHeaderException;
use TerryApiBundle\ValueObject\RequestHeaders;

class RequestHeadersTest extends TestCase
{
    /**
     * @Mock
     * @var HttpFoundationRequest
     */
    private \Phake_IMock $request;

    public function setUp(): void
    {
        parent::setUp();

        \Phake::initAnnotations($this);
    }

    public function testShouldReturnSerializerType()
    {
        $this->request->headers = new HeaderBag([
            'Accept' => 'application/pdf, application/xml',
            'Content-Type' => 'application/json'
        ]);

        $headers = RequestHeaders::fromRequest($this->request);

        $this->assertEquals('xml', $headers->serializerType());
    }

    /**
     * @dataProvider providerShouldThrowExceptionIfContentTypeNotSet
     */
    public function testShouldThrowExceptionIfContentTypeNotSet(array $requestHeaders)
    {
        $this->expectException(RequestHeaderException::class);

        $this->request->headers = new HeaderBag($requestHeaders);

        $headers = RequestHeaders::fromRequest($this->request);

        $headers->deserializerType();
    }

    public function providerShouldThrowExceptionIfContentTypeNotSet()
    {
        return [
            [
                []
            ],
            [
                [
                    'Content-Type' => ''
                ]
            ],
            [
                [
                    'Content-Type' => 'application/pdf'
                ]
            ],
        ];
    }

    public function testShouldReturnDeserializerType()
    {
        $this->request->headers = new HeaderBag([
            'Accept' => 'application/pdf, application/xml',
            'Content-Type' => 'application/json'
        ]);

        $headers = RequestHeaders::fromRequest($this->request);

        $this->assertEquals('json', $headers->deserializerType());
    }

    public function testShouldReturnResponseHeaders()
    {
        $this->request->headers = new HeaderBag([
            'Accept' => 'application/pdf, application/xml',
            'Content-Type' => 'application/json'
        ]);

        $headers = RequestHeaders::fromRequest($this->request);

        $expected = [
            'Content-Type' => 'application/xml',
        ];

        $this->assertEquals($expected, $headers->responseHeaders());
    }

    /**
     * @dataProvider providerThrowExceptionIfContentNotNegotiatable
     */
    public function testShouldThrowExceptionIfContentNotNegotiatable(array $requestHeaders)
    {
        $this->expectException(RequestHeaderException::class);

        $this->request->headers = new HeaderBag($requestHeaders);

        $headers = RequestHeaders::fromRequest($this->request);

        $headers->responseHeaders();
    }

    public function providerThrowExceptionIfContentNotNegotiatable()
    {
        return [
            [
                [
                    'Accept' => '*/*',
                    'Content-Type' => 'application/json'
                ]
            ],
            [
                [
                    'Content-Type' => 'application/json'
                ]
            ]
        ];
    }
}

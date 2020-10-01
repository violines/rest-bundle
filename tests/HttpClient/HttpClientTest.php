<?php

declare(strict_types=1);

namespace TerryApi\Tests\HttpClient;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use TerryApiBundle\Exception\RequestHeaderException;
use TerryApiBundle\HttpClient\HttpClient;
use TerryApiBundle\HttpClient\ServerSettings;

class HttpClientTest extends TestCase
{
    private const FORMAT_SERIALIZER_MAP = [
        'application/json' => 'json',
        'application/xml' => 'xml'
    ];

    /**
     * @Mock
     * @var HttpFoundationRequest
     */
    private \Phake_IMock $request;

    public function setUp(): void
    {
        parent::setUp();

        \Phake::initAnnotations($this);
        \Phake::when($this->request)->getLocale->thenReturn('en_GB');
    }

    public function testShouldReturnSerializerType()
    {
        $this->request->headers = new HeaderBag([
            'Accept' => 'application/pdf, application/xml',
            'Content-Type' => 'application/json'
        ]);

        $client = HttpClient::new($this->request, ServerSettings::fromConfig(ServerSettings::FORMAT_DEFAULT_DEFAULT, self::FORMAT_SERIALIZER_MAP));

        $this->assertEquals('xml', $client->serializerType());
    }

    /**
     * @dataProvider providerShouldThrowExceptionIfContentTypeNotSet
     */
    public function testShouldThrowExceptionIfContentTypeNotSet(array $requestHeaders)
    {
        $this->expectException(RequestHeaderException::class);

        $this->request->headers = new HeaderBag($requestHeaders);

        $client = HttpClient::new($this->request, ServerSettings::fromConfig(ServerSettings::FORMAT_DEFAULT_DEFAULT, self::FORMAT_SERIALIZER_MAP));

        $client->deserializerType();
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

        $client = HttpClient::new($this->request, ServerSettings::fromConfig(ServerSettings::FORMAT_DEFAULT_DEFAULT, self::FORMAT_SERIALIZER_MAP));

        $this->assertEquals('json', $client->deserializerType());
    }

    /**
     * @dataProvider providerShouldNegotiateContentType
     */
    public function testShouldNegotiateContentType(array $requestHeaders, string $expected)
    {
        $this->request->headers = new HeaderBag($requestHeaders);

        $client = HttpClient::new($this->request, ServerSettings::fromConfig(ServerSettings::FORMAT_DEFAULT_DEFAULT, self::FORMAT_SERIALIZER_MAP));

        $this->assertEquals($expected, $client->negotiateContentType());
    }

    public function providerShouldNegotiateContentType()
    {
        return [
            [
                [
                    'Accept' => 'application/pdf, application/xml',
                    'Content-Type' => 'application/json'
                ],
                'application/xml'
            ],
            [
                [
                    'Accept' => '*/*',
                    'Content-Type' => 'application/xml'
                ],
                'application/json',
            ],
            [
                [
                    'Accept' => 'random/random, */*',
                    'Content-Type' => 'application/xml'
                ],
                'application/json'
            ],
            [
                [
                    'Accept' => 'application/*, random/random',
                    'Content-Type' => 'application/xml'
                ],
                'application/json'
            ],
            [
                [
                    'Accept' => 'application/xml;q=0.9,application/json;q=1.0,*/*;q=0.8',
                    'Content-Type' => 'application/xml'
                ],
                'application/json'
            ],
            [
                [
                    'Accept' => 'application/xml;q=0.9,application/json,*/*;q=0.8',
                    'Content-Type' => 'application/xml'
                ],
                'application/json',
            ],
            [
                [
                    'Accept' => 'application/xml;q=0.9,text/html;q=0.8,*/*',
                    'Content-Type' => 'application/xml'
                ],
                'application/json',
            ]
        ];
    }

    /**
     * @dataProvider providerThrowExceptionIfContentNotNegotiatable
     */
    public function testShouldThrowExceptionIfContentNotNegotiatable(array $requestHeaders)
    {
        $this->expectException(RequestHeaderException::class);

        $this->request->headers = new HeaderBag($requestHeaders);

        $client = HttpClient::new($this->request, ServerSettings::fromConfig(ServerSettings::FORMAT_DEFAULT_DEFAULT, self::FORMAT_SERIALIZER_MAP));

        $client->negotiateContentType();
    }

    public function providerThrowExceptionIfContentNotNegotiatable()
    {
        return [
            [
                [
                    'Accept' => '',
                    'Content-Type' => 'application/xml'
                ],
            ],
            [
                [
                    'Accept' => 'randomstringButNotEmpty',
                    'Content-Type' => 'application/json'
                ]
            ],
            [
                [
                    'Accept' => 'application/random',
                    'Content-Type' => 'application/json'
                ]
            ]
        ];
    }
}

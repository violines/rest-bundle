<?php

declare(strict_types=1);

namespace TerryApi\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use TerryApiBundle\Tests\Stubs\HTTPClient;
use TerryApiBundle\ValueObject\HTTPServer;

class AbstactHTTPClientTest extends TestCase
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
        \Phake::when($this->request)->getLocale->thenReturn('en_GB');
    }

    public function testShouldGetProperties()
    {
        $this->request->headers = new HeaderBag(self::VALID_HEADERS);

        $clientMock = HTTPClient::fromRequest($this->request, new HTTPServer());

        $this->assertEquals('application/pdf, application/xml', $clientMock->get('accept'));
        $this->assertEquals('ISO-8859-1,utf-8;q=0.7,*;q=0.7', $clientMock->get('acceptCharset'));
        $this->assertEquals('br, gzip;q=0.8', $clientMock->get('acceptEncoding'));
        $this->assertEquals('en-GB', $clientMock->get('acceptLanguage'));
        $this->assertEquals('application/json', $clientMock->get('contentType'));
    }

    public function testShouldNegotiate()
    {
        $this->request->headers = new HeaderBag(self::VALID_HEADERS);

        $clientMock = HTTPClient::fromRequest($this->request, new HTTPServer());

        $this->assertEquals('application/xml', $clientMock->negotiateProperty('application/pdf, application/xml', 'accept', [], ['application/xml']));
        $this->assertEquals('utf-8', $clientMock->negotiateProperty('ISO-8859-1;q=0.7,utf-8,*;q=0.7', 'acceptCharset', [], ['ISO-8859-1', 'utf-8']));
        $this->assertEquals('gzip', $clientMock->negotiateProperty('br, gzip;q=0.8', 'acceptEncoding', [], ['gzip']));
        $this->assertEquals('en-GB', $clientMock->negotiateProperty('en-GB', 'acceptLanguage', [], ['en-GB']));
    }

    public function testShouldReturnEmptyHeaderValueStrings()
    {
        $this->request->headers = new HeaderBag(self::NULL_HEADERS);

        $clientMock = HTTPClient::fromRequest($this->request, new HTTPServer());

        $this->assertEquals('', $clientMock->get('accept'));
        $this->assertEquals('', $clientMock->get('acceptCharset'));
        $this->assertEquals('', $clientMock->get('acceptEncoding'));
        $this->assertEquals('', $clientMock->get('acceptLanguage'));
        $this->assertEquals('', $clientMock->get('contentType'));
    }

    private const VALID_HEADERS = [
        'Accept' => 'application/pdf, application/xml',
        'Accept-Charset' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
        'Accept-Encoding' => 'br, gzip;q=0.8',
        'Accept-Language' => 'en-GB',
        'Content-Type' => 'application/json'
    ];

    private const NULL_HEADERS = [
        'Accept' => null,
        'Accept-Charset' => null,
        'Accept-Encoding' => null,
        'Accept-Language' => null,
        'Content-Type' => null
    ];
}

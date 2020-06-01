<?php

declare(strict_types=1);

namespace TerryApi\Tests\ArgumentValueResolver;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use TerryApiBundle\ArgumentValueResolver\AbstractHTTPClientResolver;
use TerryApiBundle\Tests\Stubs\HTTPClient;
use TerryApiBundle\ValueObject\HTTPServer;

class AbstractHTTPClientResolverTest extends TestCase
{
    /**
     * @Mock
     * @var ArgumentMetadata
     */
    private \Phake_IMock $argument;

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

        $this->resolver = new AbstractHTTPClientResolver(new HTTPServer());
    }

    /**
     * @dataProvider providerShouldYieldClient
     */
    public function testShouldYieldClient(string $type)
    {
        \Phake::when($this->argument)->getType->thenReturn($type);
        $this->request->headers = new HeaderBag(self::HEADERS);

        $supports = $this->resolver->supports($this->request, $this->argument);
        $this->assertTrue($supports);

        $result = $this->resolver->resolve($this->request, $this->argument);
        $this->assertInstanceOf(HTTPClient::class, $result->current());
    }

    public function providerShouldYieldClient(): array
    {
        return [
            [HTTPClient::class],
        ];
    }

    /**
     * @dataProvider providerShouldReturnSupportsFalse
     */
    public function testShouldReturnSupportsFalse(?string $type)
    {
        \Phake::when($this->argument)->getType->thenReturn($type);
        $this->request->headers = new HeaderBag(self::HEADERS);

        $supports = $this->resolver->supports($this->request, $this->argument);
        $this->assertFalse($supports);
    }

    public function providerShouldReturnSupportsFalse(): array
    {
        return [
            [null],
            ['string'],
        ];
    }

    /**
     * @dataProvider providerShouldThrowException
     */
    public function testShouldThrowException(?string $type)
    {
        \Phake::when($this->argument)->getType->thenReturn($type);
        $this->request->headers = new HeaderBag(self::HEADERS);

        $this->expectException(\LogicException::class);
        $result = $this->resolver->resolve($this->request, $this->argument);
        $result->current();
    }

    public function providerShouldThrowException(): array
    {
        return [
            [null],
            ['string'],
        ];
    }

    private const HEADERS = [
        'Accept' => 'application/pdf, application/xml',
        'Accept-Charset' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
        'Accept-Encoding' => 'br, gzip;q=0.8',
        'Accept-Language' => 'en-GB',
        'Content-Type' => 'application/json'
    ];
}

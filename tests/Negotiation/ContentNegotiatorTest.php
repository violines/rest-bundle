<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Negotiation;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Error\RequestHeaderException;
use TerryApiBundle\Negotiation\ContentNegotiator;
use TerryApiBundle\Request\AcceptHeader;
use TerryApiBundle\Tests\Stubs\Config;
use TerryApiBundle\Tests\Stubs\MimeTypes;

class ContentNegotiatorTest extends TestCase
{
    private ContentNegotiator $contentNegotiator;

    public function setUp(): void
    {
        parent::setUp();

        $this->contentNegotiator = new ContentNegotiator(Config::SERIALIZE_FORMATS, Config::SERIALIZE_FORMAT_DEFAULT);
    }

    /**
     * @dataProvider providerShouldNegotiateContentType
     */
    public function testShouldNegotiateContentType(string $expected, string $accept)
    {
        $accept = AcceptHeader::fromString($accept);

        $this->assertEquals($expected, $this->contentNegotiator->negotiate($accept)->toString());
    }

    public function providerShouldNegotiateContentType()
    {
        return [
            [MimeTypes::APPLICATION_XML, 'application/pdf, application/xml'],
            [MimeTypes::APPLICATION_JSON, '*/*'],
            [MimeTypes::APPLICATION_JSON, 'random/random, */*'],
            [MimeTypes::APPLICATION_JSON, 'application/*, random/random'],
            [MimeTypes::APPLICATION_JSON, 'application/xml;q=0.9,application/json;q=1.0,*/*;q=0.8'],
            [MimeTypes::APPLICATION_JSON, 'application/xml;q=0.9,application/json,*/*;q=0.8'],
            [MimeTypes::APPLICATION_JSON, 'application/xml;q=0.9,text/html;q=0.8,*/*'],
            [MimeTypes::APPLICATION_JSON, ''],
            [MimeTypes::APPLICATION_JSON, 'randomstringButNotEmpty'],
            [MimeTypes::APPLICATION_JSON, 'application/random']
        ];
    }
}

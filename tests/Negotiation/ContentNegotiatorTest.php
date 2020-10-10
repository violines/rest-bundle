<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Negotiation;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Error\RequestHeaderException;
use TerryApiBundle\Negotiation\ContentNegotiator;
use TerryApiBundle\Request\AcceptHeader;

class ContentNegotiatorTest extends TestCase
{
    private const SERIALIZE_FORMATS = [
        'json' => [
            'application/json'
        ],
        'xml' => [
            'application/xml'
        ]
    ];

    private const SERIALIZE_FORMAT_DEFAULT = 'application/json';

    private ContentNegotiator $contentNegotiator;

    public function setUp(): void
    {
        parent::setUp();

        $this->contentNegotiator = new ContentNegotiator(self::SERIALIZE_FORMATS, self::SERIALIZE_FORMAT_DEFAULT);
    }

    /**
     * @dataProvider providerShouldNegotiateContentType
     */
    public function testShouldNegotiateContentType(string $accept, string $expected)
    {
        $accept = AcceptHeader::fromString($accept);

        $this->assertEquals($expected, $this->contentNegotiator->negotiate($accept)->toString());
    }

    public function providerShouldNegotiateContentType()
    {
        return [
            [
                'application/pdf, application/xml',
                'application/xml'
            ],
            [
                '*/*',
                'application/json'
            ],
            [
                'random/random, */*',
                'application/json'
            ],
            [
                'application/*, random/random',
                'application/json'
            ],
            [
                'application/xml;q=0.9,application/json;q=1.0,*/*;q=0.8',
                'application/json'
            ],
            [
                'application/xml;q=0.9,application/json,*/*;q=0.8',
                'application/json',
            ],
            [
                'application/xml;q=0.9,text/html;q=0.8,*/*',
                'application/json',
            ]
        ];
    }

    /**
     * @dataProvider providerThrowExceptionIfContentNotNegotiatable
     */
    public function testShouldThrowExceptionIfContentNotNegotiatable(string $accept)
    {
        $this->expectException(RequestHeaderException::class);

        $accept = AcceptHeader::fromString($accept);

        $this->contentNegotiator->negotiate($accept);
    }

    public function providerThrowExceptionIfContentNotNegotiatable()
    {
        return [
            [
                ''
            ],
            [
                'randomstringButNotEmpty'
            ],
            [
                'application/random'
            ]
        ];
    }
}

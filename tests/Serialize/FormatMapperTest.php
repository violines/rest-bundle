<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Serialize;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Negotiation\MimeType;
use TerryApiBundle\Serialize\FormatException;
use TerryApiBundle\Serialize\FormatMapper;

class FormatMapperTest extends TestCase
{
    /**
     * @dataProvider providerShouldMapMimeTypeToFormat
     */
    public function testShouldMapMimeTypeToFormat(array $serializeFormats, $givenMimeType, $expectedFormat)
    {
        $formatMapper = new FormatMapper($serializeFormats);

        $this->assertEquals($expectedFormat, $formatMapper->byMimeType(MimeType::fromString($givenMimeType)));
    }

    public function providerShouldMapMimeTypeToFormat()
    {
        return [
            [
                [
                    'json' => [
                        'application/json'
                    ]
                ],
                'application/json',
                'json'
            ],
            [
                [
                    'xml' => [
                        'application/xml',
                        'application/atom+xml'
                    ]
                ],
                'application/xml',
                'xml'
            ]
        ];
    }

    /**
     * @dataProvider providerShouldThrowException
     */
    public function testShouldThrowException(array $serializeFormats, $givenMimeType)
    {
        $this->expectException(FormatException::class);

        $formatMapper = new FormatMapper($serializeFormats);

        $formatMapper->byMimeType(MimeType::fromString($givenMimeType));
    }

    public function providerShouldThrowException()
    {
        return [
            [
                [
                    'json' => [
                        'application/json'
                    ]
                ],
                'application/xml'
            ],
            [
                [],
                'application/xml'
            ]
        ];
    }
}

<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Serialize;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Negotiation\MimeType;
use TerryApiBundle\Serialize\FormatException;
use TerryApiBundle\Serialize\FormatMapper;
use TerryApiBundle\Tests\Stubs\MimeTypes;

/**
 * @covers \TerryApiBundle\Serialize\FormatMapper
 */
class FormatMapperTest extends TestCase
{
    /**
     * @dataProvider providerShouldMapMimeTypeToFormat
     */
    public function testShouldMapMimeTypeToFormat(array $serializeFormats, $givenMimeType, $expectedFormat): void
    {
        $formatMapper = new FormatMapper($serializeFormats);

        $this->assertEquals($expectedFormat, $formatMapper->byMimeType(MimeType::fromString($givenMimeType)));
    }

    public function providerShouldMapMimeTypeToFormat(): array
    {
        return [
            [
                [
                    'json' => [
                        MimeTypes::APPLICATION_JSON
                    ]
                ],
                MimeTypes::APPLICATION_JSON,
                'json'
            ],
            [
                [
                    'xml' => [
                        MimeTypes::APPLICATION_XML,
                        'application/atom+xml'
                    ]
                ],
                MimeTypes::APPLICATION_XML,
                'xml'
            ]
        ];
    }

    /**
     * @dataProvider providerShouldThrowException
     */
    public function testShouldThrowException(array $serializeFormats, $givenMimeType): void
    {
        $this->expectException(FormatException::class);

        $formatMapper = new FormatMapper($serializeFormats);

        $formatMapper->byMimeType(MimeType::fromString($givenMimeType));
    }

    public function providerShouldThrowException(): array
    {
        return [
            [
                [
                    'json' => [
                        MimeTypes::APPLICATION_JSON
                    ]
                ],
                MimeTypes::APPLICATION_XML
            ],
            [
                [],
                MimeTypes::APPLICATION_XML
            ]
        ];
    }
}

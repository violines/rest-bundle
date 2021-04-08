<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Serialize;

use PHPUnit\Framework\TestCase;
use Violines\RestBundle\Negotiation\MimeType;
use Violines\RestBundle\Serialize\FormatException;
use Violines\RestBundle\Serialize\FormatMapper;
use Violines\RestBundle\Tests\Stubs\MimeTypes;

/**
 * @covers \Violines\RestBundle\Serialize\FormatMapper
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
                        MimeTypes::APPLICATION_JSON,
                    ],
                ],
                MimeTypes::APPLICATION_JSON,
                'json',
            ],
            [
                [
                    'xml' => [
                        MimeTypes::APPLICATION_XML,
                        'application/atom+xml',
                    ],
                ],
                MimeTypes::APPLICATION_XML,
                'xml',
            ],
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
                        MimeTypes::APPLICATION_JSON,
                    ],
                ],
                MimeTypes::APPLICATION_XML,
            ],
            [
                [],
                MimeTypes::APPLICATION_XML,
            ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace TerryApiBundle\Serialize;

use TerryApiBundle\Negotiation\MimeType;

final class FormatMapper
{
    /**
     * @var array<string, string>
     */
    private array $map = [];

    /**
     * @param array<string, array<string>> $serializeFormats
     */
    public function __construct(array $serializeFormats)
    {
        foreach ($serializeFormats as $format => $mimeTypes) {
            foreach ($mimeTypes as $mimeType) {
                $this->map[$mimeType] = $format;
            }
        }
    }

    /**
     * @throws FormatException when the mimeType was not mapped to a format in config
     */
    public function byMimeType(MimeType $mimeType): string
    {
        if (!isset($this->map[$mimeType->toString()])) {
            throw FormatException::notConfigured($mimeType);
        }

        return $this->map[$mimeType->toString()];
    }
}

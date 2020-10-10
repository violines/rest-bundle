<?php

declare(strict_types=1);

namespace TerryApiBundle\Serialize;

final class TypeMapper
{
    /**
     * @var array<string, string>
     */
    private array $map;

    /**
     * @param array<string, array<string>> $serializeformats
     */
    public function __construct(array $serializeformats)
    {
        foreach ($serializeformats as $type => $mimeTypes) {
            foreach ($mimeTypes as $mimeType) {
                $this->map[$mimeType] = $type;
            }
        }
    }

    public function getType(Format $format): string
    {
        if (!isset($this->map[$format->toString()])) {
            throw FormatException::notConfigured($format);
        }

        return $this->map[$format->toString()];
    }
}

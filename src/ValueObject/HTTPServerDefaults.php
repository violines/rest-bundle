<?php

declare(strict_types=1);

namespace TerryApiBundle\ValueObject;

class HTTPServerDefaults
{
    private const FORMAT_DEFAULT_DEFAULT = 'application/json';

    private const FORMAT_SERIALIZER_MAP_DEFAULT = [
        'application/json' => 'json',
        'application/xml' => 'xml'
    ];

    private string $formatDefault;

    private array $formatSerializerMap;

    public function __construct(
        ?string $formatDefault = null,
        ?array $formatSerializerMap = null
    ) {
        $this->formatDefault = $formatDefault ?? self::FORMAT_DEFAULT_DEFAULT;
        $this->formatSerializerMap = array_replace(
            self::FORMAT_SERIALIZER_MAP_DEFAULT,
            (array) $formatSerializerMap
        );
    }

    public function getFormatDefault(): string
    {
        return $this->formatDefault;
    }

    public function getFormatSerializerMap(): array
    {
        return $this->formatSerializerMap;
    }
}

<?php

declare(strict_types=1);

namespace TerryApiBundle\ValueObject;

class HTTPServer
{
    private const FORMAT_DEFAULT_DEFAULT = 'application/json';

    private const FORMAT_SERIALIZER_MAP_DEFAULT = ['application/json' => 'json'];

    private string $formatDefault;

    private array $formatSerializerMap;

    public function __construct(
        string $formatDefault = '',
        array $formatSerializerMap = []
    ) {
        $this->formatDefault = '' !== $formatDefault ? $formatDefault : self::FORMAT_DEFAULT_DEFAULT;
        $this->formatSerializerMap = [] !== $formatSerializerMap ? $formatSerializerMap : self::FORMAT_SERIALIZER_MAP_DEFAULT;
    }

    public function formatDefault(): string
    {
        return $this->formatDefault;
    }

    public function formatSerializerMap(): array
    {
        return $this->formatSerializerMap;
    }
}

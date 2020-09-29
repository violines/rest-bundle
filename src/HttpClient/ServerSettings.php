<?php

declare(strict_types=1);

namespace TerryApiBundle\HttpClient;

final class ServerSettings
{
    private const FORMAT_DEFAULT_DEFAULT = 'application/json';

    private const FORMAT_SERIALIZER_MAP_DEFAULT = ['application/json' => 'json'];

    private string $formatDefault;

    /**
     * @var array<string, string>
     */
    private array $formatSerializerMap;

    /**
     * @param array<string, string> $formatSerializerMap
     */
    public function __construct(
        string $formatDefault = '',
        array $formatSerializerMap = []
    ) {
        $this->formatDefault = '' !== $formatDefault ? $formatDefault : self::FORMAT_DEFAULT_DEFAULT;
        $this->formatSerializerMap = [] !== $formatSerializerMap ? $formatSerializerMap : self::FORMAT_SERIALIZER_MAP_DEFAULT;
    }

    public function getFormatDefault(): string
    {
        return $this->formatDefault;
    }

    /**
     * @return array<string, string>
     */
    public function getFormatSerializerMap(): array
    {
        return $this->formatSerializerMap;
    }
}

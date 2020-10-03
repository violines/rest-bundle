<?php

declare(strict_types=1);

namespace TerryApiBundle\HttpClient;

final class ServerSettings
{
    public const FORMAT_DEFAULT_DEFAULT = 'application/json';
    public const FORMAT_SERIALIZER_MAP_DEFAULT = ['application/json' => 'json'];
    private string $formatDefault;

    /**
     * @var array<string, string>
     */
    private array $formatSerializerMap;

    /**
     * @param array<string, string> $formatSerializerMap
     */
    private function __construct(string $formatDefault, array $formatSerializerMap)
    {
        $this->formatDefault = $formatDefault;
        $this->formatSerializerMap = $formatSerializerMap;
    }

    /**
     * @param array<string, string> $formatSerializerMap
     */
    public static function fromConfig(string $formatDefault, array $formatSerializerMap): self
    {
        return new self($formatDefault, $formatSerializerMap);
    }

    public static function fromDefaults(): self
    {
        return new self(self::FORMAT_DEFAULT_DEFAULT, self::FORMAT_SERIALIZER_MAP_DEFAULT);
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

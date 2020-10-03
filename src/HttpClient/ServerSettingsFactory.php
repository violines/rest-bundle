<?php

declare(strict_types=1);

namespace TerryApiBundle\HttpClient;

final class ServerSettingsFactory
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function fromConfig(): ServerSettings
    {
        if (!isset($this->config['formats']) || !isset($this->config['format_default'])) {
            return ServerSettings::fromDefaults();
        }

        /** @var array<string, array<string, string>> $configformats */
        $configformats = $this->config['formats'];

        /** @var string $configformatDefault */
        $configformatDefault = $this->config['format_default'];

        $formatSerializerMap = [];

        foreach ($configformats as $serializerFormat => $mimeTypes) {
            foreach ($mimeTypes as $mimeType) {
                $formatSerializerMap[$mimeType] = $serializerFormat;
            }
        }

        return ServerSettings::fromConfig($configformatDefault, $formatSerializerMap);
    }
}

<?php

declare(strict_types=1);

namespace TerryApiBundle\HttpClient;

class ServerSettingsFactory
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function fromConfig(): ServerSettings
    {
        /** @var array<string, array<string, string>> $configformats */
        $configformats = $this->config['formats'] ?? [];
        /** @var string $configformatDefault */
        $configformatDefault = $this->config['format_default'] ?? '';

        $_formatSerializerMap = [];

        foreach ($configformats as $serializerFormat => $mimeTypes) {
            foreach ($mimeTypes as $mimeType) {
                $_formatSerializerMap[$mimeType] = $serializerFormat;
            }
        }

        return new ServerSettings($configformatDefault, $_formatSerializerMap);
    }
}

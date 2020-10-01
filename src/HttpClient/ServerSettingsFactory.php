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
        /** @var array<string, array<string, string>>|null $configformats */
        $configformats = $this->config['formats'];

        /** @var string|null $configformatDefault */
        $configformatDefault = $this->config['format_default'];

        if (!isset($configformats) || !isset($configformatDefault)) {
            return ServerSettings::fromDefaults();
        }

        $formatSerializerMap = [];

        foreach ($configformats as $serializerFormat => $mimeTypes) {
            foreach ($mimeTypes as $mimeType) {
                $formatSerializerMap[$mimeType] = $serializerFormat;
            }
        }

        return ServerSettings::fromConfig($configformatDefault, $formatSerializerMap);
    }
}

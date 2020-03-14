<?php

declare(strict_types=1);

namespace TerryApiBundle\Factory;

use TerryApiBundle\ValueObject\HTTPServerDefaults;

class HTTPServerDefaultsFactory
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function fromConfig(): HTTPServerDefaults
    {
        $configformats = $this->config['format'] ?? null;
        $configformatDefault = $this->config['format_default'] ?? null;

        $_formatSerializerMap = [];

        foreach ((array) $configformats as $serializerFormat => $mimeTypes) {
            foreach ($mimeTypes as $mimeType) {
                $_formatSerializerMap[$mimeType] = $serializerFormat;
            }
        }

        return new HTTPServerDefaults($configformatDefault, $_formatSerializerMap);
    }
}

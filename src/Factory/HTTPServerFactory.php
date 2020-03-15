<?php

declare(strict_types=1);

namespace TerryApiBundle\Factory;

use TerryApiBundle\ValueObject\HTTPServer;

class HTTPServerFactory
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function fromConfig(): HTTPServer
    {
        $configformats = $this->config['formats'] ?? null;
        $configformatDefault = $this->config['format_default'] ?? null;

        $_formatSerializerMap = [];

        foreach ((array) $configformats as $serializerFormat => $mimeTypes) {
            foreach ($mimeTypes as $mimeType) {
                $_formatSerializerMap[$mimeType] = $serializerFormat;
            }
        }

        return new HTTPServer($configformatDefault, $_formatSerializerMap);
    }
}

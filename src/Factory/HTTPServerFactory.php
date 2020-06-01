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

        return new HTTPServer($configformatDefault, $_formatSerializerMap);
    }
}

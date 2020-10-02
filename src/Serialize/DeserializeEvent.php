<?php

declare(strict_types=1);

namespace TerryApiBundle\Serialize;

use TerryApiBundle\HttpClient\HttpClient;

final class DeserializeEvent
{
    public const NAME = 'terry_api.event.deserialize';

    private string $data;
    private HTTPClient $httpClient;
    private array $context = [];

    public function __construct(string $data, HttpClient $httpClient)
    {
        $this->data = $data;
        $this->httpClient = $httpClient;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getHttpClient(): HTTPClient
    {
        return $this->httpClient;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function mergeToContext(array $context): void
    {
        $this->context = array_merge($this->context, $context);
    }
}

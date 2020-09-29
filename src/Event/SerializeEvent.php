<?php

declare(strict_types=1);

namespace TerryApiBundle\Event;

use TerryApiBundle\HttpClient\HttpClient;

final class SerializeEvent
{
    public const NAME = 'terry_api.event.serialize';

    /**
     * @var object[]|object|array $data
     */
    private $data;
    private HTTPClient $httpClient;
    private array $context = [];

    /**
     * @param object[]|object|array $data
     */
    public function __construct($data, HttpClient $httpClient)
    {
        $this->data = $data;
        $this->httpClient = $httpClient;
    }

    /**
     * @return object[]|object|array
     */
    public function getData()
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

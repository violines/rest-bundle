<?php

declare(strict_types=1);

namespace TerryApiBundle\Event;

use TerryApiBundle\ValueObject\HTTPClient;

final class SerializeContextEvent
{
    public const NAME = 'terry_api.event.serialize_context_event';

    private array $context = [];

    /**
     * @var object[]|object|array $data
     */
    private $data;

    private HTTPClient $httpClient;

    /**
     * @param object[]|object|array $data
     */
    public function __construct($data, HTTPClient $httpClient)
    {
        $this->data = $data;
        $this->httpClient = $httpClient;
    }

    public function mergeToContext(array $context): void
    {
        $this->context = array_merge($this->context, $context);
    }

    public function getContext(): array
    {
        return $this->context;
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
}

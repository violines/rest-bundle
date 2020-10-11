<?php

declare(strict_types=1);

namespace TerryApiBundle\Serialize;

final class SerializeEvent
{
    public const NAME = 'terry_api.event.serialize';
    /**
     * @var object[]|object|array $data
     */
    private $data;
    private string $format;
    private array $context = [];

    /**
     * @param object[]|object|array $data
     */
    public function __construct($data, string $format)
    {
        $this->data = $data;
        $this->format = $format;
    }

    /**
     * @return object[]|object|array
     */
    public function getData()
    {
        return $this->data;
    }

    public function getFormat(): string
    {
        return $this->format;
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

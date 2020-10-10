<?php

declare(strict_types=1);

namespace TerryApiBundle\Serialize;

final class DeserializeEvent
{
    public const NAME = 'terry_api.event.deserialize';
    private string $data;
    private Format $format;
    private array $context = [];

    public function __construct(string $data, Format $format)
    {
        $this->data = $data;
        $this->format = $format;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getFormat(): string
    {
        return $this->format->toString();
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

<?php

declare(strict_types=1);

namespace Violines\RestBundle\Serialize;

final class DeserializeEvent
{
    public const NAME = 'violines_rest.event.deserialize';
    private string $data;
    private string $format;
    private array $context = [];

    public function __construct(string $data, string $format)
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

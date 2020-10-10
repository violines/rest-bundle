<?php

declare(strict_types=1);

namespace TerryApiBundle\Serialize;

final class Format
{
    private $format;

    public function __construct(string $format)
    {
        $this->format = $format;
    }

    public static function fromString(string $format): self
    {
        return new self($format);
    }

    public function toString(): string
    {
        return $this->format;
    }
}

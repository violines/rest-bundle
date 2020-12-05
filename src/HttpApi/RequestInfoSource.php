<?php

declare(strict_types=1);

namespace TerryApiBundle\HttpApi;

final class RequestInfoSource
{
    private const ENUM_VALUES = [HttpApi::BODY, HttpApi::QUERY_STRING];

    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        if (!\in_array($value, self::ENUM_VALUES)) {
            throw HttpApiParameterException::enum('requestInfoSource', $value, self::ENUM_VALUES);
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}

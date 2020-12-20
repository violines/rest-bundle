<?php

declare(strict_types=1);

namespace Violines\RestBundle\HttpApi;

/**
 * @internal
 */
final class RequestInfoSource
{
    private const ENUM_VALUES = [HttpApi::BODY, HttpApi::QUERY_STRING];

    private string $requestInfoSource;

    private function __construct(string $requestInfoSource)
    {
        $this->requestInfoSource = $requestInfoSource;
    }

    public static function fromString(string $requestInfoSource): self
    {
        if (!\in_array($requestInfoSource, self::ENUM_VALUES)) {
            throw HttpApiParameterException::enum('requestInfoSource', $requestInfoSource, self::ENUM_VALUES);
        }

        return new self($requestInfoSource);
    }

    public function toString(): string
    {
        return $this->requestInfoSource;
    }
}

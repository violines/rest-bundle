<?php

declare(strict_types=1);

namespace TerryApiBundle\HttpApi;

final class HttpApiParameterException extends \RuntimeException implements \Throwable
{
    private function __construct(string $message)
    {
        $this->message = $message;
    }

    public static function enum(string $parameterName, string $value, array $expected): self
    {
        return new self(sprintf('The value %s for the parameter \'%s\' for \'#[HttpApi]\' or \'@HttpApi\' is not allowed. Expected values: %s.', $value, $parameterName, \json_encode($expected)));
    }
}

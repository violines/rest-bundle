<?php

declare(strict_types=1);

namespace Violines\RestBundle\HttpApi;

/**
 * @internal
 */
final class MissingHttpApiException extends \RuntimeException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function className(string $className): self
    {
        return new self(\sprintf('\'#[HttpApi]\' or \'@HttpApi\' for %s not found.', $className));
    }
}

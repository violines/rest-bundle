<?php

declare(strict_types=1);

namespace Violines\RestBundle\Request;

/**
 * @internal
 */
final class SupportsException extends \LogicException
{
    public static function covered(): self
    {
        return new self('This should have been covered by self::supports(). This is a bug, please report.');
    }
}

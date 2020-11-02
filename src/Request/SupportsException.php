<?php

declare(strict_types=1);

namespace TerryApiBundle\Request;

final class SupportsException extends \LogicException implements \Throwable
{
    public static function covered(): self
    {
        return new self('This should have been covered by self::supports(). This is a bug, please report.');
    }
}

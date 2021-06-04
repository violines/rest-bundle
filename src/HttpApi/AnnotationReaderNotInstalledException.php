<?php

declare(strict_types=1);

namespace Violines\RestBundle\HttpApi;

/**
 * @internal
 */
final class AnnotationReaderNotInstalledException extends \RuntimeException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function doctrine(): self
    {
        return new self('Could not find any class that implements Doctrine\Common\Annotations\Reader. Install e.g. with \'composer req doctrine/annotations\' or use native PHP Attributes.');
    }
}

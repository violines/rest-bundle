<?php

declare(strict_types=1);

namespace TerryApiBundle\HttpApi;

final class AnnotationReaderNotInstalledException extends \RuntimeException implements \Throwable
{
    private function __construct(string $message)
    {
        $this->message = $message;
    }

    public static function doctrine(): self
    {
        return new self('Could not find any class that implements Doctrine\Common\Annotations\Reader. Install e.g. with \'composer req doctrine/annotations\' or use native PHP Attributes.');
    }
}

<?php

declare(strict_types=1);

namespace TerryApiBundle\Exception;

class AnnotationNotFoundException extends \RuntimeException implements \Throwable
{
    private function __construct(string $message)
    {
        $this->message = $message;
    }

    public static function httpApi(string $className): self
    {
        return new self(
            sprintf('Annotation \'@HTTPApi\' for %s not found.', $className)
        );
    }
}

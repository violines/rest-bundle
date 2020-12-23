<?php

declare(strict_types=1);

namespace Violines\RestBundle\Response;

/**
 * @internal
 */
final class ContentTypeHeader
{
    public const NAME = 'Content-Type';
    private const PROBLEM = 'problem+';
    private string $contentType;

    private function __construct(string $contentType)
    {
        $this->contentType = $contentType;
    }

    public static function fromString(string $contentType): self
    {
        return new self($contentType);
    }

    public function toString(): string
    {
        return $this->contentType;
    }

    public function toStringWithProblem(): string
    {
        return $this->withProblem($this->contentType);
    }

    private function withProblem(string $contentType): string
    {
        $problemContentType = '';

        $parts = explode('/', $contentType);

        $limit = count($parts) - 1;

        for ($i = $limit; $i >= 0; $i--) {
            if ($i !== $limit) {
                $problemContentType = '/' . $problemContentType;
            }

            $problemContentType = $parts[$i] . $problemContentType;

            if ($i === $limit) {
                $problemContentType = self::PROBLEM . $problemContentType;
            }
        }

        return $problemContentType;
    }
}

<?php

declare(strict_types=1);

namespace Violines\RestBundle\Error;

use Violines\RestBundle\HttpApi\HttpApi;

/**
 * @HttpApi
 *
 * @internal
 */
#[HttpApi]
final class Error
{
    private const DEFAULT_TYPE = "about:blank";
    private const DEFAULT_TITLE = "General Error";
    private string $type;
    private string $title;
    private string $detail;

    private function __construct(string $detail, string $title, string $type)
    {
        $this->detail = $detail;
        $this->title = $title;
        $this->type = $type;
    }

    public static function new(string $detail, string $title = self::DEFAULT_TITLE, string $type = self::DEFAULT_TYPE): self
    {
        return new self($detail, $title, $type);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDetail(): string
    {
        return $this->detail;
    }
}

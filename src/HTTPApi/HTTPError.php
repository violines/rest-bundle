<?php

declare(strict_types=1);

namespace TerryApiBundle\HTTPApi;

use TerryApiBundle\Annotation\HTTPApi;

/**
 * @HTTPApi
 */
class HTTPError
{
    private const DEFAULT_TYPE = "about:blank";
    private const DEFAULT_TITLE =  "General Error";

    public string $type;

    public string $title;

    public string $detail;

    private function __construct(string $detail, ?string $title, ?string $type)
    {
        $this->detail = $detail;
        $this->title = $title ?? self::DEFAULT_TITLE;
        $this->type = $type ?? self::DEFAULT_TYPE;
    }

    public static function create(string $detail, ?string $title = null, ?string $type = null): self
    {
        return new self($detail, $title, $type);
    }
}

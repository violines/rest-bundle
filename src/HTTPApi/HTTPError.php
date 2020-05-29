<?php

declare(strict_types=1);

namespace TerryApiBundle\HTTPApi;

use TerryApiBundle\Annotation\HTTPApi;

/**
 * @HTTPApi
 */
class HTTPError
{
    public string $message;

    private function __construct(string $message)
    {
        $this->message = $message;
    }

    public static function fromMessage(string $message): self
    {
        return new self($message);
    }
}

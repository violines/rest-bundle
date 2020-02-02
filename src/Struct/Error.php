<?php

declare(strict_types=1);

namespace TerryApiBundle\Struct;

use TerryApiBundle\Annotation\Struct;

/**
 * @Struct
 */
class Error
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

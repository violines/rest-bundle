<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Stubs;

use TerryApiBundle\Annotation\Struct;

/**
 * @Struct
 */
class ErrorStructStub
{
    public $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}

<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Stubs;

use TerryApiBundle\HttpApi\HttpApi;

/**
 * @HttpApi
 */
class Ok
{
    public $message = "Everything is fine.";

    public static function create(): self
    {
        return new self();
    }
}

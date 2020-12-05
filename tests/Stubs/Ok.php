<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Stubs;

use Violines\RestBundle\HttpApi\HttpApi;

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

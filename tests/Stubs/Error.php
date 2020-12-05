<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Stubs;

use Violines\RestBundle\HttpApi\HttpApi;

/**
 * @HttpApi
 */
class Error
{
    public $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}

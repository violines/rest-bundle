<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Stubs;

use TerryApiBundle\Annotation\HTTPApi;

/**
 * @HTTPApi
 */
class Error
{
    public $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}

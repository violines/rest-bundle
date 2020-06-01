<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Stubs;

use TerryApiBundle\Annotation\HTTPApi;

/**
 * @HTTPApi
 */
class Ok
{
    public $message = "Everything is fine.";

    public static function create()
    {
        return new self();
    }
}

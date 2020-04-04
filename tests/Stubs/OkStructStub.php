<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Stubs;

use TerryApiBundle\Annotation\Struct;

/**
 * @Struct
 */
class OkStructStub
{
    public $message = "Everything is fine.";

    public static function create()
    {
        return new self();
    }
}

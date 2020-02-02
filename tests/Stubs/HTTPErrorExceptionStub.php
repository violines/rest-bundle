<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Stubs;

use TerryApiBundle\Exception\HTTPErrorInterface;

class HTTPErrorExceptionStub extends \LogicException implements \Throwable, HTTPErrorInterface
{
    public function getStruct(): object
    {
        return new CandyStructStub();
    }

    public function getHTTPStatusCode(): int
    {
        return 400;
    }
}

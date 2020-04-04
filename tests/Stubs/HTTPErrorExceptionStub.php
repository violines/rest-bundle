<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Stubs;

use TerryApiBundle\Exception\HTTPErrorInterface;

class HTTPErrorExceptionStub extends \LogicException implements \Throwable, HTTPErrorInterface
{
    public function getStruct(): object
    {
        return $this->struct;
    }

    public function getHTTPStatusCode(): int
    {
        return 400;
    }

    public function setStructToStruct(): void
    {
        $this->struct = new ErrorStructStub('Test 400');
    }

    public function setStructToNonStructObject(): void
    {
        $this->struct = new GumModelStub();
    }
}

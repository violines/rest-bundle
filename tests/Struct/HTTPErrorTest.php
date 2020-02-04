<?php

declare(strict_types=1);

namespace TerryApi\Tests\Struct;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Struct\HTTPError;

class HTTPErrorTest extends TestCase
{
    public function testShouldCreateHTTPErrorStruct()
    {
        $message = 'This is the reason for an error.';

        $struct = HTTPError::fromMessage($message);

        $this->assertEquals($struct->message, $message);
    }
}

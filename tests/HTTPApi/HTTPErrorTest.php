<?php

declare(strict_types=1);

namespace TerryApi\Tests\HTTPApi;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\HTTPApi\HTTPError;

class HTTPErrorTest extends TestCase
{
    public function testShouldCreateHTTPErrorStruct()
    {
        $message = 'This is the reason for an error.';

        $content = HTTPError::fromMessage($message);

        $this->assertEquals($content->message, $message);
    }
}

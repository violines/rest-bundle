<?php

declare(strict_types=1);

namespace TerryApi\Tests\HTTPApi;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\HTTPApi\HTTPError;

class HTTPErrorTest extends TestCase
{
    public function testShouldCreateHTTPError()
    {
        $message = 'This is the reason for an error.';

        $content = HTTPError::create($message);

        $this->assertEquals($content->detail, $message);
        $this->assertEquals($content->type, 'about:blank');
        $this->assertEquals($content->title, 'General Error');
    }
}

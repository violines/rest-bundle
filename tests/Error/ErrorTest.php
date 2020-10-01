<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Error;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Error\Error;

class ErrorTest extends TestCase
{
    public function testShouldCreateHTTPError()
    {
        $message = 'This is the reason for an error.';

        $content = Error::new($message);

        $this->assertEquals($content->getType(), 'about:blank');
        $this->assertEquals($content->getTitle(), 'General Error');
        $this->assertEquals($content->getDetail(), $message);
    }
}

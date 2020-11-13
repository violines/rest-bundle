<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Error;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Error\Error;

/**
 * @covers \TerryApiBundle\Error\Error
 */
class ErrorTest extends TestCase
{
    public function testShouldCreateHttpError(): void
    {
        $message = 'This is the reason for an error.';

        $content = Error::new($message);

        $this->assertEquals('about:blank', $content->getType());
        $this->assertEquals('General Error', $content->getTitle());
        $this->assertEquals($message, $content->getDetail());
    }
}

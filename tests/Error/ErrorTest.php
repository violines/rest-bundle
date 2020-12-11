<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Error;

use PHPUnit\Framework\TestCase;
use Violines\RestBundle\Error\Error;
use Violines\RestBundle\HttpApi\HttpApi;

/**
 * @covers \Violines\RestBundle\Error\Error
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

    /**
     * @requires PHP >= 8.0
     */
    public function testShouldHaveAttribute(): void
    {
        $reflectionClass = new \ReflectionClass(Error::class);

        $this->assertInstanceOf(HttpApi::class, $reflectionClass->getAttributes(HttpApi::class)[0]->newInstance());
    }
}

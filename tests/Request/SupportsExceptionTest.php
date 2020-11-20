<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Request\SupportsException;

/**
 * @covers \TerryApiBundle\Request\EmptyBodyException
 */
class SupportsExceptionTest extends TestCase
{
    public function testShouldCreateSupportsException(): void
    {
        $this->assertInstanceOf(SupportsException::class, SupportsException::covered());
    }
}

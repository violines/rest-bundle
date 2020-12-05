<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use Violines\RestBundle\Request\SupportsException;

/**
 * @covers \Violines\RestBundle\Request\EmptyBodyException
 */
class SupportsExceptionTest extends TestCase
{
    public function testShouldCreateSupportsException(): void
    {
        $this->assertInstanceOf(SupportsException::class, SupportsException::covered());
    }
}

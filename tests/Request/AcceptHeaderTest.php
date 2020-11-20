<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Request\AcceptHeader;

/**
 * @covers \TerryApiBundle\Request\AcceptHeader
 */
class AcceptHeaderTest extends TestCase
{
    private const ACCEPT = '*/*';

    public function testShouldCreateAcceptHeader(): void
    {
        $accept = AcceptHeader::fromString(self::ACCEPT);

        $this->assertInstanceOf(AcceptHeader::class, $accept);
        $this->assertEquals(self::ACCEPT, $accept->toString());
    }
}

<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use Violines\RestBundle\Request\AcceptHeader;

/**
 * @covers \Violines\RestBundle\Request\AcceptHeader
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

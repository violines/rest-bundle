<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Negotiation;

use PHPUnit\Framework\TestCase;
use Violines\RestBundle\Negotiation\MimeType;

/**
 * @covers \Violines\RestBundle\Negotiation\MimeType
 */
class MimeTypeTest extends TestCase
{
    public function testShouldCreateMimeType(): void
    {
        $mimeType = MimeType::fromString('application/json');

        self::assertInstanceOf(MimeType::class, $mimeType);
        self::assertEquals('application/json', $mimeType->toString());
    }
}

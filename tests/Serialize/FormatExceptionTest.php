<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Negotiation;

use PHPUnit\Framework\TestCase;
use Violines\RestBundle\Negotiation\MimeType;
use Violines\RestBundle\Serialize\FormatException;

/**
 * @covers \Violines\RestBundle\Serialize\FormatException
 *
 * @uses \Violines\RestBundle\Negotiation\MimeType
 */
class FormatExceptionTest extends TestCase
{
    public function testShouldCreateMimeType(): void
    {
        $exception = FormatException::notConfigured(MimeType::fromString('text/txt'));

        self::assertInstanceOf(FormatException::class, $exception);
        self::assertEquals('MimeType text/txt was not configured for any Format. Check configuration under serialize > formats', $exception->getMessage());
    }
}

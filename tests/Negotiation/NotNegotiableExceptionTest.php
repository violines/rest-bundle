<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Negotiation;

use PHPUnit\Framework\TestCase;
use Violines\RestBundle\Negotiation\NotNegotiableException;

/**
 * @covers \Violines\RestBundle\Negotiation\NotNegotiableException
 */
class NotNegotiableExceptionTest extends TestCase
{
    public function testShouldCreateMimeType(): void
    {
        $exception = NotNegotiableException::notConfigured('text/txt');

        self::assertInstanceOf(NotNegotiableException::class, $exception);
        self::assertEquals('None of the accepted mimetypes text/txt are configured for any Format. Check configuration under serialize > formats', $exception->getMessage());
    }
}

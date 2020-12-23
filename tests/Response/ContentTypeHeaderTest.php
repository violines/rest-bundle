<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Response;

use PHPUnit\Framework\TestCase;
use Violines\RestBundle\Response\ContentTypeHeader;

/**
 * @covers \Violines\RestBundle\Response\ContentTypeHeader
 */
class ContentTypeHeaderTest extends TestCase
{
    private const CONTENT_TYPE = 'application/json';

    public function testShouldCreateContentTypeHeader(): void
    {
        $contentType = ContentTypeHeader::fromString(self::CONTENT_TYPE);

        $this->assertInstanceOf(ContentTypeHeader::class, $contentType);
        $this->assertEquals(self::CONTENT_TYPE, $contentType->toString());
        $this->assertEquals('application/problem+json', $contentType->toStringWithProblem());
    }
}

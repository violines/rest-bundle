<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use Violines\RestBundle\Negotiation\MimeType;
use Violines\RestBundle\Request\ContentTypeHeader;

/**
 * @covers \Violines\RestBundle\Request\ContentTypeHeader
 */
class ContentTypeHeaderTest extends TestCase
{
    private const CONTENT_TYPE = 'application/json';

    public function testShouldCreateContentTypeHeader(): void
    {
        $contentType = ContentTypeHeader::fromString(self::CONTENT_TYPE);

        $this->assertInstanceOf(ContentTypeHeader::class, $contentType);
        $this->assertEquals(self::CONTENT_TYPE, $contentType->toString());

        $this->assertInstanceOf(MimeType::class, $contentType->toMimeType());
    }
}

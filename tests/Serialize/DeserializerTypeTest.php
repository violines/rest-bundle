<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Serialize;

use PHPUnit\Framework\TestCase;
use Violines\RestBundle\Serialize\DeserializerType;

/**
 * @covers \Violines\RestBundle\Serialize\DeserializerType
 */
class DeserializerTypeTest extends TestCase
{
    public function testShouldCreateForObject(): void
    {
        $type = DeserializerType::object(TestObject::class);

        self::assertSame(TestObject::class, $type->toString());
    }

    public function testShouldCreateForArray(): void
    {
        $type = DeserializerType::array(TestObject::class);

        self::assertSame(TestObject::class . '[]', $type->toString());
    }
}

final class TestObject
{
}

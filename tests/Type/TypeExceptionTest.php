<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Type;

use PHPUnit\Framework\TestCase;
use Violines\RestBundle\Type\TypeException;

/**
 * @covers \Violines\RestBundle\Type\TypeException
 */
class TypeExceptionTest extends TestCase
{
    public function testShouldCreateTypeException(): void
    {
        $this->assertInstanceOf(TypeException::class, TypeException::notObject());
        $this->assertInstanceOf(TypeException::class, TypeException::notSameClass());
    }
}

<?php

declare(strict_types=1);

namespace TerryApi\Tests\Exception;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Exception\AnnotationNotFoundException;

class AnnotationNotFoundExceptionTest extends TestCase
{
    public function testShouldStruct()
    {
        $exception = AnnotationNotFoundException::struct('Classname');

        $this->assertInstanceOf(AnnotationNotFoundException::class, $exception);
    }
}

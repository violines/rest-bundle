<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Exception;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Exception\AnnotationNotFoundException;

class AnnotationNotFoundExceptionTest extends TestCase
{
    public function testShouldStruct()
    {
        $exception = AnnotationNotFoundException::httpApi('Classname');

        $this->assertInstanceOf(AnnotationNotFoundException::class, $exception);
    }
}

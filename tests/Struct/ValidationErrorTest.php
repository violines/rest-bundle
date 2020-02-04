<?php

declare(strict_types=1);

namespace TerryApi\Tests\Struct;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Struct\ValidationError;
use TerryApiBundle\Tests\Stubs\ConstraintViolationListStub;

class ValidationErrorTest extends TestCase
{
    public function testShouldCreateHTTPErrorStruct()
    {
        $struct = ValidationError::fromViolations(new ConstraintViolationListStub());

        $this->assertInstanceOf(ValidationError::class, $struct);
    }
}

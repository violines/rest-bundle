<?php

declare(strict_types=1);

namespace TerryApi\Tests\Struct;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Struct\ValidationError;
use TerryApiBundle\Struct\ValidationErrorViolation;
use TerryApiBundle\Tests\Stubs\ConstraintViolationListStub;
use TerryApiBundle\Tests\Stubs\ConstraintViolationStub;

class ValidationErrorTest extends TestCase
{
    public function testShouldCreateHTTPErrorStruct()
    {
        $list = new ConstraintViolationListStub();

        $list->add(new ConstraintViolationStub());
        $list->add(new ConstraintViolationStub());

        $struct = ValidationError::fromViolations($list);

        $this->assertInstanceOf(ValidationError::class, $struct);
        $this->assertInstanceOf(ValidationErrorViolation::class, $struct->violations[0]);
        $this->assertInstanceOf(ValidationErrorViolation::class, $struct->violations[1]);
    }
}

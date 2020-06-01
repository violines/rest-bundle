<?php

declare(strict_types=1);

namespace TerryApi\Tests\HTTPApi;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\HTTPApi\ValidationError;
use TerryApiBundle\HTTPApi\ValidationErrorViolation;
use TerryApiBundle\Tests\Stubs\ConstraintViolationList;
use TerryApiBundle\Tests\Stubs\ConstraintViolation;

class ValidationErrorTest extends TestCase
{
    public function testShouldCreateHTTPErrorStruct()
    {
        $list = new ConstraintViolationList();

        $list->add(new ConstraintViolation());
        $list->add(new ConstraintViolation());

        $content = ValidationError::fromViolations($list);

        $this->assertInstanceOf(ValidationError::class, $content);
        $this->assertInstanceOf(ValidationErrorViolation::class, $content->violations[0]);
        $this->assertInstanceOf(ValidationErrorViolation::class, $content->violations[1]);
    }
}

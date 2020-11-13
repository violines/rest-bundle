<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Serialize;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Serialize\SerializeEvent;

/**
 * @covers \TerryApiBundle\Serialize\SerializeEvent
 */
class SerializeEventTest extends TestCase
{
    public function testShouldCreateEvent(): void
    {
        $data = [];
        $format = 'json';
        $context = ['firstKey' => 'firstVal', 'secondkey' => 'secondVal'];

        $serializeContextEvent = new SerializeEvent($data, $format);

        $serializeContextEvent->mergeToContext(['firstKey' => 'firstVal']);
        $serializeContextEvent->mergeToContext(['secondkey' => 'secondVal']);

        $this->assertEquals($data, $serializeContextEvent->getData());
        $this->assertEquals($format, $serializeContextEvent->getFormat());
        $this->assertEquals($context, $serializeContextEvent->getContext());
    }
}

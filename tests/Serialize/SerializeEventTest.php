<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Serialize;

use PHPUnit\Framework\TestCase;
use Violines\RestBundle\Serialize\SerializeEvent;

/**
 * @covers \Violines\RestBundle\Serialize\SerializeEvent
 */
class SerializeEventTest extends TestCase
{
    public function testShouldCreateEvent(): void
    {
        $data = [];
        $format = 'json';
        $context = ['firstKey' => 'firstVal', 'secondkey' => 'secondVal'];

        $serializeEvent = SerializeEvent::from($data, $format);

        $serializeEvent->mergeToContext(['firstKey' => 'firstVal']);
        $serializeEvent->mergeToContext(['secondkey' => 'secondVal']);

        $this->assertEquals($data, $serializeEvent->getData());
        $this->assertEquals($format, $serializeEvent->getFormat());
        $this->assertEquals($context, $serializeEvent->getContext());
    }
}

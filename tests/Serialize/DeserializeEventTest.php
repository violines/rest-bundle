<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Serialize;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Serialize\DeserializeEvent;
use TerryApiBundle\Serialize\Format;

class DeserializeEventTest extends TestCase
{
    public function testShouldCreateEvent()
    {
        $data = '{"weight": 100, "name": "Bonbon", "tastesGood": true}';
        $format = Format::fromString('application/json');
        $context = ['firstKey' => 'firstVal', 'secondkey' => 'secondVal'];

        $serializeContextEvent = new DeserializeEvent($data, $format);

        $serializeContextEvent->mergeToContext(['firstKey' => 'firstVal']);
        $serializeContextEvent->mergeToContext(['secondkey' => 'secondVal']);

        $this->assertEquals($data, $serializeContextEvent->getData());
        $this->assertEquals($format->toString(), $serializeContextEvent->getFormat());
        $this->assertEquals($context, $serializeContextEvent->getContext());
    }
}

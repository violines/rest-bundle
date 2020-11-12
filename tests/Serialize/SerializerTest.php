<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Serialize;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use TerryApiBundle\Negotiation\MimeType;
use TerryApiBundle\Serialize\DeserializeEvent;
use TerryApiBundle\Serialize\SerializeEvent;
use TerryApiBundle\Serialize\FormatMapper;
use TerryApiBundle\Serialize\Serializer;
use TerryApiBundle\Tests\Stubs\Candy;
use TerryApiBundle\Tests\Stubs\Config;

class SerializerTest extends TestCase
{
    /**
     * @Mock
     * @var EventDispatcherInterface
     */
    private \Phake_IMock $eventDispatcher;

    /**
     * @Mock
     * @var SerializerInterface
     */
    private \Phake_IMock $serializerInterface;

    public function setUp(): void
    {
        parent::setUp();
        \Phake::initAnnotations($this);
    }

    public function testShouldSerialize(): void
    {
        $data = [];
        $context = ['ctxkey' => 'ctxValue'];
        $mimeType = MimeType::fromString('application/json');

        $serializeContextEvent = new SerializeEvent($data, 'json');
        $serializeContextEvent->mergeToContext($context);

        \Phake::when($this->eventDispatcher)->dispatch->thenReturn($serializeContextEvent);
        \Phake::when($this->serializerInterface)->serialize->thenReturn('[]');

        $serializer = new Serializer($this->eventDispatcher, $this->serializerInterface, new FormatMapper(Config::SERIALIZE_FORMATS));
        $serializer->serialize($data, $mimeType);

        \Phake::verify($this->serializerInterface)->serialize($data, 'json', $context);
    }

    public function testShouldDeserialize(): void
    {
        $data = '{"weight": 100, "name": "Bonbon", "tastesGood": true}';
        $type = 'TerryApiBundle\Tests\Stubs\CandyStructStub';
        $context = ['ctxkey' => 'ctxValue'];
        $mimeType = MimeType::fromString('application/json');

        $deserializeEvent = new DeserializeEvent($data, 'json');
        $deserializeEvent->mergeToContext($context);

        \Phake::when($this->eventDispatcher)->dispatch->thenReturn($deserializeEvent);
        \Phake::when($this->serializerInterface)->serialize->thenReturn(new Candy());

        $serializer = new Serializer($this->eventDispatcher, $this->serializerInterface, new FormatMapper(Config::SERIALIZE_FORMATS));
        $serializer->deserialize($data, $type, $mimeType);

        \Phake::verify($this->serializerInterface)->deserialize($data, $type, 'json', $context);
    }
}

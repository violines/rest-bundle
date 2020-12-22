<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Serialize;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Violines\RestBundle\Negotiation\MimeType;
use Violines\RestBundle\Serialize\DeserializeEvent;
use Violines\RestBundle\Serialize\FormatMapper;
use Violines\RestBundle\Serialize\SerializeEvent;
use Violines\RestBundle\Serialize\Serializer;
use Violines\RestBundle\Tests\Stubs\Config;

/**
 * @covers \Violines\RestBundle\Serialize\Serializer
 *
 * @uses \Violines\RestBundle\Serialize\DeserializeEvent
 * @uses \Violines\RestBundle\Serialize\SerializeEvent
 */
class SerializerTest extends TestCase
{
    /**
     * @Mock
     *
     * @var EventDispatcherInterface
     */
    private \Phake_IMock $eventDispatcher;

    /**
     * @Mock
     *
     * @var SerializerInterface
     */
    private \Phake_IMock $serializerInterface;

    protected function setUp(): void
    {
        parent::setUp();
        \Phake::initAnnotations($this);
    }

    public function testShouldSerialize(): void
    {
        $data = [];
        $context = ['ctxkey' => 'ctxValue'];
        $mimeType = MimeType::fromString('application/json');

        $serializeContextEvent = SerializeEvent::from($data, 'json');
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
        $type = 'Violines\RestBundle\Tests\Stubs\CandyStructStub';
        $context = ['ctxkey' => 'ctxValue'];
        $mimeType = MimeType::fromString('application/json');

        $deserializeEvent = DeserializeEvent::from($data, 'json');
        $deserializeEvent->mergeToContext($context);

        \Phake::when($this->eventDispatcher)->dispatch->thenReturn($deserializeEvent);
        \Phake::when($this->serializerInterface)->serialize->thenReturn(new Candy());

        $serializer = new Serializer($this->eventDispatcher, $this->serializerInterface, new FormatMapper(Config::SERIALIZE_FORMATS));
        $serializer->deserialize($data, $type, $mimeType);

        \Phake::verify($this->serializerInterface)->deserialize($data, $type, 'json', $context);
    }
}

/**
 * @HttpApi
 */
class Candy
{
    public int $weight = 100;
    public string $name = 'Bonbon';
    public bool $tastes_good = true;
}

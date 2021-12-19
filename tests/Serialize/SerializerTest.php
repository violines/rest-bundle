<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Serialize;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Violines\RestBundle\Negotiation\MimeType;
use Violines\RestBundle\Serialize\DeserializeEvent;
use Violines\RestBundle\Serialize\DeserializerType;
use Violines\RestBundle\Serialize\FormatMapper;
use Violines\RestBundle\Serialize\SerializeEvent;
use Violines\RestBundle\Serialize\Serializer;
use Violines\RestBundle\Tests\Stub\Config;

/**
 * @covers \Violines\RestBundle\Serialize\Serializer
 *
 * @uses \Violines\RestBundle\Serialize\DeserializerType
 * @uses \Violines\RestBundle\Serialize\DeserializeEvent
 * @uses \Violines\RestBundle\Serialize\SerializeEvent
 */
class SerializerTest extends TestCase
{
    use ProphecyTrait;

    public function testShouldVerifyContextMergeOnSerialize(): void
    {
        $data = [];
        $context = ['ctxkey' => 'ctxValue'];
        $mimeType = MimeType::fromString('application/json');

        $serializeContextEvent = SerializeEvent::from($data, 'json');
        $serializeContextEvent->mergeToContext($context);

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::type(SerializeEvent::class), SerializeEvent::NAME)->willReturn($serializeContextEvent);

        $symfonySerializer = $this->prophesize(SerializerInterface::class);
        $symfonySerializer->serialize($data, 'json', $context)->willReturn('string');
        $symfonySerializer->serialize($data, 'json', $context)->shouldBeCalled();

        $serializer = new Serializer($eventDispatcher->reveal(), $symfonySerializer->reveal(), new FormatMapper(Config::SERIALIZE_FORMATS));
        $serializer->serialize($data, $mimeType);
    }

    public function testShouldVerifyContextMergeOnDeserialize(): void
    {
        $data = '{"weight": 100, "name": "Bonbon", "tastesGood": true}';
        $type = DeserializerType::object(Product::class);
        $context = ['ctxkey' => 'ctxValue'];
        $mimeType = MimeType::fromString('application/json');

        $deserializeEvent = DeserializeEvent::from($data, 'json');
        $deserializeEvent->mergeToContext($context);

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::type(DeserializeEvent::class), DeserializeEvent::NAME)->willReturn($deserializeEvent);

        $symfonySerializer = $this->prophesize(SerializerInterface::class);
        $symfonySerializer->deserialize($data, $type->toString(), 'json', $context)->willReturn(new Product(100, 'Bonbon', true));
        $symfonySerializer->deserialize($data, $type->toString(), 'json', $context)->shouldBeCalled();

        $serializer = new Serializer($eventDispatcher->reveal(), $symfonySerializer->reveal(), new FormatMapper(Config::SERIALIZE_FORMATS));
        $serializer->deserialize($data, $type, $mimeType);
    }
}

/**
 * @HttpApi
 */
final class Product
{
    public int $weight;
    public string $name;
    public bool $tastes_good;

    public function __construct(int $weight, string $name, bool $tastesGood)
    {
        $this->weight = $weight;
        $this->name = $name;
        $this->tastes_good = $tastesGood;
    }
}

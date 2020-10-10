<?php

declare(strict_types=1);

namespace TerryApiBundle\Serialize;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class Serializer
{
    private EventDispatcherInterface $eventDispatcher;
    private SerializerInterface $serializer;
    private TypeMapper $typeMapper;

    public function __construct(EventDispatcherInterface $eventDispatcher, SerializerInterface $serializer, TypeMapper $typeMapper)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->serializer = $serializer;
        $this->typeMapper = $typeMapper;
    }

    /**
     * @param object[]|object|array $data
     */
    public function serialize($data, Format $format): string
    {
        /** @var SerializeEvent $serializeEvent */
        $serializeEvent = $this->eventDispatcher->dispatch(
            new SerializeEvent($data, $format),
            SerializeEvent::NAME
        );

        return $this->serializer->serialize(
            $data,
            $this->typeMapper->getType($format),
            $serializeEvent->getContext()
        );
    }

    /**
     * @return mixed[]|object
     */
    public function deserialize(string $data, string $type, Format $format)
    {
        /** @var DeserializeEvent $deserializeEvent */
        $deserializeEvent = $this->eventDispatcher->dispatch(
            new DeserializeEvent($data, $format),
            DeserializeEvent::NAME
        );

        return $this->serializer->deserialize(
            $data,
            $type,
            $this->typeMapper->getType($format),
            $deserializeEvent->getContext()
        );
    }
}

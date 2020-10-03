<?php

declare(strict_types=1);

namespace TerryApiBundle\Serialize;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use TerryApiBundle\HttpClient\HttpClient;

final class Serializer
{
    private EventDispatcherInterface $eventDispatcher;

    private SerializerInterface $serializer;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        SerializerInterface $serializer
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->serializer = $serializer;
    }

    /**
     * @param object[]|object|array $data
     */
    public function serialize($data, HttpClient $httpClient): string
    {
        /** @var SerializeEvent $serializeEvent */
        $serializeEvent = $this->eventDispatcher->dispatch(
            new SerializeEvent($data, $httpClient),
            SerializeEvent::NAME
        );

        return $this->serializer->serialize(
            $data,
            $httpClient->serializerType(),
            $serializeEvent->getContext()
        );
    }

    /**
     * @return mixed[]|object
     */
    public function deserialize(string $data, string $type, HttpClient $httpClient)
    {
        /** @var DeserializeEvent $deserializeEvent */
        $deserializeEvent = $this->eventDispatcher->dispatch(
            new DeserializeEvent($data, $httpClient),
            DeserializeEvent::NAME
        );

        return $this->serializer->deserialize(
            $data,
            $type,
            $httpClient->deserializerType(),
            $deserializeEvent->getContext()
        );
    }
}

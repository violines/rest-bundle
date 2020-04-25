<?php

declare(strict_types=1);

namespace TerryApiBundle\Facade;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use TerryApiBundle\Event\DeserializeEvent;
use TerryApiBundle\Event\SerializeEvent;
use TerryApiBundle\ValueObject\HTTPClient;

class SerializerFacade
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
    public function serialize($data, HTTPClient $httpClient): string
    {
        $serializeContextEvent = $this->eventDispatcher->dispatch(
            new SerializeEvent($data, $httpClient),
            SerializeEvent::NAME
        );

        return $this->serializer->serialize(
            $data,
            $httpClient->serializerType(),
            $serializeContextEvent->getContext()
        );
    }

    /**
     * @return object[]|object
     */
    public function deserialize(string $data, string $type, HTTPClient $httpClient)
    {
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

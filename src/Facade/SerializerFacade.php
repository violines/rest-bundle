<?php

declare(strict_types=1);

namespace TerryApiBundle\Facade;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use TerryApiBundle\Event\SerializeContextEvent;
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
            new SerializeContextEvent($data, $httpClient),
            SerializeContextEvent::NAME
        );

        return $this->serializer->serialize(
            $data,
            $httpClient->serializerType(),
            $serializeContextEvent->getContext()
        );
    }
}

<?php

declare(strict_types=1);

namespace TerryApiBundle\Serialize;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use TerryApiBundle\Negotiation\MimeType;

final class Serializer
{
    private EventDispatcherInterface $eventDispatcher;
    private SerializerInterface $serializer;
    private FormatMapper $formatMapper;

    public function __construct(EventDispatcherInterface $eventDispatcher, SerializerInterface $serializer, FormatMapper $formatMapper)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->serializer = $serializer;
        $this->formatMapper = $formatMapper;
    }

    /**
     * @param object[]|object|array $data
     */
    public function serialize($data, MimeType $mimeType): string
    {
        $format = $this->formatMapper->byMimeType($mimeType);

        /** @var SerializeEvent $serializeEvent */
        $serializeEvent = $this->eventDispatcher->dispatch(new SerializeEvent($data, $format), SerializeEvent::NAME);

        return $this->serializer->serialize($data, $format, $serializeEvent->getContext());
    }

    /**
     * @return mixed[]|object
     */
    public function deserialize(string $data, string $type, MimeType $mimeType)
    {
        $format = $this->formatMapper->byMimeType($mimeType);

        /** @var DeserializeEvent $deserializeEvent */
        $deserializeEvent = $this->eventDispatcher->dispatch(new DeserializeEvent($data, $format), DeserializeEvent::NAME);

        return $this->serializer->deserialize($data, $type, $format, $deserializeEvent->getContext());
    }
}

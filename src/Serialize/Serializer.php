<?php

declare(strict_types=1);

namespace Violines\RestBundle\Serialize;

use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Violines\RestBundle\Negotiation\MimeType;

/**
 * @internal
 */
final class Serializer implements SerializerInterface
{
    private EventDispatcherInterface $eventDispatcher;
    private SymfonySerializerInterface $serializer;
    private FormatMapper $formatMapper;

    public function __construct(EventDispatcherInterface $eventDispatcher, SymfonySerializerInterface $serializer, FormatMapper $formatMapper)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->serializer = $serializer;
        $this->formatMapper = $formatMapper;
    }

    /**
     * @param object[]|object $data
     */
    public function serialize($data, MimeType $mimeType): string
    {
        $format = $this->formatMapper->byMimeType($mimeType);

        /** @var SerializeEvent $serializeEvent */
        $serializeEvent = $this->eventDispatcher->dispatch(SerializeEvent::from($data, $format), SerializeEvent::NAME);

        return $this->serializer->serialize($data, $format, $serializeEvent->getContext());
    }

    /**
     * @return object[]|object
     */
    public function deserialize(string $data, DeserializerType $type, MimeType $mimeType)
    {
        $format = $this->formatMapper->byMimeType($mimeType);

        /** @var DeserializeEvent $deserializeEvent */
        $deserializeEvent = $this->eventDispatcher->dispatch(DeserializeEvent::from($data, $format), DeserializeEvent::NAME);

        /** @var object[]|object $deserialized */
        $deserialized = $this->serializer->deserialize($data, $type->toString(), $format, $deserializeEvent->getContext());

        return $deserialized;
    }
}

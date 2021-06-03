<?php

namespace Violines\RestBundle\Serialize;

use Violines\RestBundle\Negotiation\MimeType;

interface SerializerInterface
{
    /**
     * @param object[]|object $data
     */
    public function serialize($data, MimeType $mimeType): string;

    /**
     * @return object[]|object
     */
    public function deserialize(string $data, DeserializerType $type, MimeType $mimeType);
}

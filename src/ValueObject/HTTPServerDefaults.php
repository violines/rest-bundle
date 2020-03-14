<?php

declare(strict_types=1);

namespace TerryApiBundle\ValueObject;

class HTTPServerDefaults
{
    private const CONTENT_TYPE_DEFAULT_DEFAULT = 'application/json';

    private const CONTENT_TYPE_SERIALIZER_MAP_DEFAULT = [
        'application/json' => 'json',
        'application/xml' => 'xml'
    ];

    private string $contentTypeDefault;

    private array $contentTypeSerializerMap;

    public function __construct(
        ?string $contentTypeDefault = null,
        ?array $contentTypeSerializerMap = null
    ) {
        $this->contentTypeDefault = $contentTypeDefault ?? self::CONTENT_TYPE_DEFAULT_DEFAULT;
        $this->contentTypeSerializerMap = array_replace(
            self::CONTENT_TYPE_SERIALIZER_MAP_DEFAULT,
            (array) $contentTypeSerializerMap
        );
    }

    public function getContentTypeDefault(): string
    {
        return $this->contentTypeDefault;
    }

    public function getContentTypeSerializerMap(): array
    {
        return $this->contentTypeSerializerMap;
    }
}

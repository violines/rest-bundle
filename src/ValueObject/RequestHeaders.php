<?php

declare(strict_types=1);

namespace TerryApiBundle\ValueObject;

use Symfony\Component\HttpFoundation\Request;

class RequestHeaders
{
    private const CONTENT_TYPE_SERIALIZER_MAP = [
        'application/json' => 'json'
    ];

    private string $contentType;

    private function __construct(string $contentType)
    {
        $this->contentType = $contentType;
    }

    public static function fromRequest(Request $request): self
    {
        $contentType = $request->getContentType() ?? 'application/json';

        return new self($contentType);
    }

    public function serializerType(): string
    {
        return array_key_exists($this->contentType, self::CONTENT_TYPE_SERIALIZER_MAP)
            ? self::CONTENT_TYPE_SERIALIZER_MAP[$this->contentType]
            : 'json';
    }
}

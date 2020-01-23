<?php

declare(strict_types=1);

namespace TerryApiBundle\ValueObject;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use TerryApiBundle\Exception\RequestHeaderException;

class RequestHeaders
{
    public const CONTENT_TYPE = 'Content-Type';

    private const CONTENT_TYPE_SERIALIZER_MAP = [
        'application/json' => 'json',
        'text/html' => 'xml'
    ];

    private string $contentType;

    private function __construct(HeaderBag $headers)
    {
        $this->contentType = (string) $headers->get(self::CONTENT_TYPE, '');
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request->headers);
    }

    public function serializerType(): string
    {
        if (!isset(self::CONTENT_TYPE_SERIALIZER_MAP[$this->contentType])) {
            throw new RequestHeaderException(
                sprintf('Content-Type Header value %s cannot be processed.', $this->contentType)
            );
        }

        return self::CONTENT_TYPE_SERIALIZER_MAP[$this->contentType];
    }

    public function responseHeaders(): array
    {
        return [
            self::CONTENT_TYPE => $this->contentType
        ];
    }
}

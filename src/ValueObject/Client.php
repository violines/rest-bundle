<?php

declare(strict_types=1);

namespace TerryApiBundle\ValueObject;

use Symfony\Component\HttpFoundation\Request;
use TerryApiBundle\Exception\RequestHeaderException;

class Client extends AbstractClient
{
    private const CONTENT_TYPE_DEFAULTS_MAP = [
        '*/*' => 'application/json',
        'application/*' => 'application/json'
    ];

    private const CONTENT_TYPE_SERIALIZER_MAP = [
        'application/json' => 'json',
        'application/xml' => 'xml'
    ];

    public static function fromRequest(Request $request): self
    {
        return new self($request->headers);
    }

    public function serializerType(): string
    {
        return self::CONTENT_TYPE_SERIALIZER_MAP[$this->negotiateContentType()];
    }

    public function deserializerType(): string
    {
        if (!isset(self::CONTENT_TYPE_SERIALIZER_MAP[$this->contentType])) {
            throw RequestHeaderException::valueNotAllowed(self::CONTENT_TYPE, $this->contentType);
        }

        return self::CONTENT_TYPE_SERIALIZER_MAP[$this->contentType];
    }

    public function responseHeaders(): array
    {
        return [
            self::CONTENT_TYPE => $this->negotiateContentType()
        ];
    }

    private function negotiateContentType(): string
    {
        return $this->negotiate(
            $this->accept,
            self::ACCEPT,
            self::CONTENT_TYPE_DEFAULTS_MAP,
            array_keys(self::CONTENT_TYPE_SERIALIZER_MAP)
        );
    }
}

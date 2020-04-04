<?php

declare(strict_types=1);

namespace TerryApiBundle\ValueObject;

use Symfony\Component\HttpFoundation\Request;
use TerryApiBundle\Exception\RequestHeaderException;

class HTTPClient extends AbstractHTTPClient
{
    private const CONTENT_TYPE_DEFAULT_KEYS = ['*/*', 'application/*'];

    private array $contentTypeDefaultsMap = [];

    private array $formatSerializerMap = [];

    public static function fromRequest(
        Request $request,
        HTTPServer $httpServer
    ): self {
        $client = new self($request);
        $client->setContentTypeDefaultsMap($httpServer->formatDefault());
        $client->setFormatSerializerMap($httpServer->formatSerializerMap());
        return $client;
    }

    public function setContentTypeDefaultsMap(string $formatDefault): void
    {
        foreach (self::CONTENT_TYPE_DEFAULT_KEYS as $key) {
            $this->contentTypeDefaultsMap[$key] = $formatDefault;
        }
    }

    public function setFormatSerializerMap(array $formatSerializerMap): void
    {
        $this->formatSerializerMap = $formatSerializerMap;
    }

    public function serializerType(): string
    {
        return $this->formatSerializerMap[$this->negotiateContentType()];
    }

    public function deserializerType(): string
    {
        if (!isset($this->formatSerializerMap[$this->contentType()])) {
            throw RequestHeaderException::valueNotAllowed(self::CONTENT_TYPE, $this->contentType());
        }

        return $this->formatSerializerMap[$this->contentType()];
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
            $this->accept(),
            self::ACCEPT,
            $this->contentTypeDefaultsMap,
            array_keys($this->formatSerializerMap)
        );
    }
}

<?php

declare(strict_types=1);

namespace TerryApiBundle\ValueObject;

use Symfony\Component\HttpFoundation\Request;
use TerryApiBundle\Exception\RequestHeaderException;

class HTTPClient extends AbstractHTTPClient
{
    private const CONTENT_TYPE_DEFAULT_KEYS = ['*/*', 'application/*'];

    private array $contentTypeDefaultsMap = [];

    /**
     * @var array<string, string>
     */
    private array $formatSerializerMap;

    protected function __construct(Request $request, HTTPServer $httpServer)
    {
        parent::__construct($request);

        foreach (self::CONTENT_TYPE_DEFAULT_KEYS as $key) {
            $this->contentTypeDefaultsMap[$key] = $httpServer->formatDefault();
        }

        $this->formatSerializerMap = $httpServer->formatSerializerMap();
    }

    public static function fromRequest(Request $request, HTTPServer $httpServer): self
    {
        return new self($request, $httpServer);
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

    public function negotiateContentType(): string
    {
        return $this->negotiate(
            $this->accept(),
            self::ACCEPT,
            $this->contentTypeDefaultsMap,
            array_keys($this->formatSerializerMap)
        );
    }

    public function symfonyLocale(): string
    {
        return $this->locale();
    }
}

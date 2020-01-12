<?php

declare(strict_types=1);

namespace TerryApiBundle\ValueObject;

use Symfony\Component\HttpFoundation\Request;
use TerryApiBundle\Exception\RequestHeaderException;

class RequestHeaders
{
    private const HEADER_PROPERTY_MAP = [
        'Content-Type' => 'contentType',
    ];

    private const CONTENT_TYPE_SERIALIZER_MAP = [
        'application/json' => 'json'
    ];

    private string $contentType;

    private function __construct()
    {
    }

    public static function fromRequest(Request $request): self
    {
        $requestHeaders = new self();

        foreach (self::HEADER_PROPERTY_MAP as $hKey => $property) {
            $header = $request->headers->get($hKey);

            if (null === $header) {
                throw new RequestHeaderException(
                    sprintf('The %s Header is missing in the request.', $hKey)
                );
            }

            $requestHeaders->$property = $header;
        }

        return $requestHeaders;
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
            'Content-Type' => $this->contentType
        ];
    }
}

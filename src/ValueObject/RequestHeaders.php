<?php

declare(strict_types=1);

namespace TerryApiBundle\ValueObject;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use TerryApiBundle\Exception\RequestHeaderException;

class RequestHeaders
{
    public const ACCEPT = 'Accept';
    public const CONTENT_TYPE = 'Content-Type';

    private const CONTENT_TYPE_DEFAULTS_MAP = [
        '*/*' => 'application/json',
        'application/*' => 'application/json'
    ];

    private const CONTENT_TYPE_SERIALIZER_MAP = [
        'application/json' => 'json',
        'application/xml' => 'xml'
    ];

    private string $accept;

    private string $contentType;

    private function __construct(HeaderBag $headers)
    {
        $this->accept = (string) $headers->get(self::ACCEPT, '');
        $this->contentType = (string) $headers->get(self::CONTENT_TYPE, '');
    }

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
        $accept = strtr($this->accept, self::CONTENT_TYPE_DEFAULTS_MAP);

        $accepts = explode(',', $accept);

        /** string $accept */
        foreach ($accepts as $accept) {
            $type = trim($accept, ' ');
            if (isset(self::CONTENT_TYPE_SERIALIZER_MAP[$type])) {
                return $type;
            }
        }

        throw RequestHeaderException::valueNotAllowed(self::ACCEPT, $this->accept);
    }
}

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
        $accepts = explode(
            ',',
            strtr(
                preg_replace("@[ 　]@u", '', $this->accept),
                self::CONTENT_TYPE_DEFAULTS_MAP
            )
        );

        $_accepts = [];
        foreach ($accepts as $accept) {
            $splited = explode(';', $accept);
            $key = $splited[1] ?? 'q=1.0';
            if (
                array_key_exists($splited[0], self::CONTENT_TYPE_SERIALIZER_MAP)
                && !array_key_exists($key, $_accepts)
            ) {
                $_accepts[$key] = $splited[0];
            }
        }

        krsort($_accepts);

        /** string $_accept */
        foreach ($_accepts as $_accept) {
            return $_accept;
        }

        throw RequestHeaderException::valueNotAllowed(self::ACCEPT, $this->accept);
    }
}

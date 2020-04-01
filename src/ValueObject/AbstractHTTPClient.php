<?php

declare(strict_types=1);

namespace TerryApiBundle\ValueObject;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use TerryApiBundle\Exception\RequestHeaderException;

abstract class AbstractHTTPClient
{
    public const ACCEPT = 'Accept';
    public const ACCEPT_CHARSET = 'Accept-Charset';
    public const ACCEPT_ENCODING = 'Accept-Encoding';
    public const ACCEPT_LANGUAGE = 'Accept-Language';
    public const CONTENT_TYPE = 'Content-Type';

    private const NEGOTIATE_DEFAULTS = [
        '*' => '',
        '*/*' => '',
        'application/*' => ''
    ];

    private HeaderBag $headers;

    private string $locale;

    protected function __construct(HeaderBag $headers, string $locale)
    {
        $this->headers = $headers;
        $this->locale = $locale;
    }

    abstract public static function fromRequest(Request $request, HTTPServer $httpServer): self;

    protected function accept(): string
    {
        return (string) $this->headers->get(self::ACCEPT, '');
    }

    protected function acceptCharset(): string
    {
        return (string) $this->headers->get(self::ACCEPT_CHARSET, '');
    }

    protected function acceptEncoding(): string
    {
        return (string) $this->headers->get(self::ACCEPT_ENCODING, '');
    }

    protected function acceptLanguage(): string
    {
        return (string) $this->headers->get(self::ACCEPT_LANGUAGE, '');
    }

    protected function contentType(): string
    {
        return (string) $this->headers->get(self::CONTENT_TYPE, '');
    }

    protected function locale(): string
    {
        return $this->locale;
    }

    protected function negotiate(
        string $subject,
        string $headerName,
        array $defaults,
        array $availables = []
    ): string {
        $defaults += self::NEGOTIATE_DEFAULTS;

        $results = explode(
            ',',
            strtr(
                preg_replace("@[ 　]@u", '', $subject),
                $defaults
            )
        );

        $_results = [];
        foreach ($results as $accept) {
            $splited = explode(';', $accept);
            $key = $splited[1] ?? 'q=1.0';
            if (
                in_array($splited[0], $availables)
                && !array_key_exists($key, $_results)
            ) {
                $_results[$key] = $splited[0];
            }
        }

        krsort($_results);

        /** string $_result */
        foreach ($_results as $_result) {
            return $_result;
        }

        throw RequestHeaderException::valueNotAllowed($headerName, $subject);
    }
}

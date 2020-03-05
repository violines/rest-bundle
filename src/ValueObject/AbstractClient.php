<?php

declare(strict_types=1);

namespace TerryApiBundle\ValueObject;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use TerryApiBundle\Exception\RequestHeaderException;

abstract class AbstractClient
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

    protected string $accept;
    protected string $acceptCharset;
    protected string $acceptEncoding;
    protected string $acceptLanguage;
    protected string $contentType;

    protected function __construct(HeaderBag $headers)
    {
        $this->accept = (string) $headers->get(self::ACCEPT, '');
        $this->acceptCharset = (string) $headers->get(self::ACCEPT_CHARSET, '');
        $this->acceptEncoding = (string) $headers->get(self::ACCEPT_ENCODING, '');
        $this->acceptLanguage = (string) $headers->get(self::ACCEPT_LANGUAGE, '');
        $this->contentType = (string) $headers->get(self::CONTENT_TYPE, '');
    }

    abstract public static function fromRequest(Request $request): self;

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

<?php

declare(strict_types=1);

namespace TerryApiBundle\HttpClient;

use Symfony\Component\HttpFoundation\Request;
use TerryApiBundle\Error\RequestHeaderException;

final class HttpClient
{
    // ACCEPT = formats: application/json, application/xml
    public const ACCEPT = 'Accept';
    // CONTENT_TYPE = format: application/json
    public const CONTENT_TYPE = 'Content-Type';
    private const CONTENT_TYPE_DEFAULT_KEYS = ['*/*', 'application/*'];
    private const NEGOTIATE_DEFAULTS = ['*' => '', '*/*' => '', 'application/*' => ''];
    private string $accept;
    private string $contentType;
    private array $contentTypeDefaultsMap = [];
    /**
     * @var array<string, string>
     */
    private array $formatSerializerMap;

    private function __construct(Request $request, ServerSettings $serverSettings)
    {
        $this->accept = (string) $request->headers->get(self::ACCEPT, '');
        $this->contentType = (string) $request->headers->get(self::CONTENT_TYPE, '');

        foreach (self::CONTENT_TYPE_DEFAULT_KEYS as $key) {
            $this->contentTypeDefaultsMap[$key] = $serverSettings->getFormatDefault();
        }

        $this->formatSerializerMap = $serverSettings->getFormatSerializerMap();
    }

    public static function new(Request $request, ServerSettings $serverSettings): self
    {
        return new self($request, $serverSettings);
    }

    public function serializerType(): string
    {
        return $this->formatSerializerMap[$this->negotiateContentType()];
    }

    public function deserializerType(): string
    {
        if (!isset($this->formatSerializerMap[$this->contentType])) {
            throw RequestHeaderException::valueNotAllowed(self::CONTENT_TYPE, $this->contentType);
        }

        return $this->formatSerializerMap[$this->contentType];
    }

    public function negotiateContentType(): string
    {
        return $this->negotiate(
            $this->accept,
            self::ACCEPT,
            $this->contentTypeDefaultsMap,
            array_keys($this->formatSerializerMap)
        );
    }

    private function negotiate(
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

<?php

declare(strict_types=1);

namespace TerryApiBundle\Negotiation;

use TerryApiBundle\Error\RequestHeaderException;
use TerryApiBundle\Request\AcceptHeader;

final class ContentNegotiator
{
    private const DEFAULT_KEYS = ['*' , '*/*', 'application/*'];
    private array $availables = [];
    private array $defaults = [];

    /**
     * @param array<string, array<string>> $serializeformats
     */
    public function __construct(array $serializeformats, string $defaultFormat)
    {
        foreach ($serializeformats as $mimeTypes) {
            foreach ($mimeTypes as $mimeType) {
                $this->availables[] = $mimeType;
            }
        }

        foreach (self::DEFAULT_KEYS as $key) {
            $this->defaults[$key] = $defaultFormat;
        }
    }

    public function negotiate(AcceptHeader $header): MimeType
    {
        $headerFormats = explode(
            ',',
            strtr(
                preg_replace("@[ 　]@u", '', $header->toString()),
                $this->defaults
            )
        );

        $resultFormats = [];
        foreach ($headerFormats as $format) {
            $splited = explode(';', $format);
            $key = $splited[1] ?? 'q=1.0';
            if (in_array($splited[0], $this->availables) && !array_key_exists($key, $resultFormats)) {
                $resultFormats[$key] = $splited[0];
            }
        }

        krsort($resultFormats);

        $firstResultFormat = current($resultFormats);

        if (false === $firstResultFormat) {
            throw RequestHeaderException::valueNotAllowed(AcceptHeader::NAME, $header->toString());
        }

        return MimeType::fromString($firstResultFormat);
    }
}

<?php

declare(strict_types=1);

namespace Violines\RestBundle\Negotiation;

use Violines\RestBundle\Request\AcceptHeader;

/**
 * @internal
 */
final class ContentNegotiator
{
    private const DEFAULT_KEYS = ['*', '*/*', 'application/*'];
    private array $availables = [];
    /** @var array<string,string> $defaults*/
    private array $defaults = [];

    /**
     * @param array<string,array<string>> $serializeformats
     *
     * @throws NotNegotiableException when the accept header cannot be matched with any mimeType
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
        $headerString = '' !== $header->toString() ? $header->toString() : $this->defaults['*'];

        $headerMimeTypes = explode(
            ',',
            strtr(
                preg_replace("@[ 　]@u", '', $headerString),
                $this->defaults
            )
        );

        $resultMimeTypes = [];
        foreach ($headerMimeTypes as $mimeType) {
            $splited = explode(';', $mimeType);
            $key = $splited[1] ?? 'q=1.0';
            if (in_array($splited[0], $this->availables) && !array_key_exists($key, $resultMimeTypes)) {
                $resultMimeTypes[$key] = $splited[0];
            }
        }

        if ([] === $resultMimeTypes) {
            throw NotNegotiableException::notConfigured($header->toString());
        }

        krsort($resultMimeTypes);

        return MimeType::fromString(current($resultMimeTypes));
    }
}

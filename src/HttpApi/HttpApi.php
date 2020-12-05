<?php

declare(strict_types=1);

namespace Violines\RestBundle\HttpApi;

/**
 * @Annotation
 * @Target("CLASS")
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class HttpApi
{
    public const BODY = 'body';
    public const QUERY_STRING = 'query_string';

    private RequestInfoSource $requestInfoSource;

    /**
     * @param array<string, string> $data
     */
    public function __construct(array $data = null, ?string $requestInfoSource = null)
    {
        $this->requestInfoSource = RequestInfoSource::fromString($requestInfoSource ?? $data['requestInfoSource'] ?? self::BODY);
    }

    public function getRequestInfoSource(): string
    {
        return $this->requestInfoSource->toString();
    }
}

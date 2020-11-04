<?php

declare(strict_types=1);

namespace TerryApiBundle\HttpApi;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class HttpApi
{
    public const BODY = 'body';
    public const QUERY_STRING = 'query_string';

    /**
     * @Enum({TerryApiBundle\HttpApi\HttpApi::BODY, TerryApiBundle\HttpApi\HttpApi::QUERY_STRING})
     */
    public string $requestInfoSource = self::BODY;
}

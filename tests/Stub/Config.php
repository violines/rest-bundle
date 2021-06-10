<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Stub;

class Config
{
    public const SERIALIZE_FORMATS = [
        'json' => [
            'application/json',
        ],
        'xml' => [
            'application/xml',
        ],
    ];

    public const SERIALIZE_FORMAT_DEFAULT = 'application/json';
}

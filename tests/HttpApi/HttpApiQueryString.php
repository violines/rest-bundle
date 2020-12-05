<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\HttpApi;

use Violines\RestBundle\HttpApi\HttpApi;

#[HttpApi(requestInfoSource: 'query_string')]
class HttpApiQueryString
{
    public int $id = 1;

    public string $name = 'name';
}

<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\HttpApi;

use TerryApiBundle\HttpApi\HttpApi;

#[HttpApi(requestInfoSource: 'query_string')]
class HttpApiQueryString
{
    public int $id = 1;

    public string $name = 'name';
}

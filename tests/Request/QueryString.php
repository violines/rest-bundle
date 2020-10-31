<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Request;

use TerryApiBundle\HttpApi\HttpApi;

/**
 * @HttpApi(requestInfoSource="query_string")
 */
class QueryString
{
    public $filterPriceFrom;
    public $filterPriceTo;
}

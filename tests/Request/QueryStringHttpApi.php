<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Request;

use TerryApiBundle\HttpApi\HttpApi;

/**
 * @HttpApi(requestInfoSource=HttpApi::QUERY_STRING)
 */
final class QueryStringHttpApi
{
    public $priceFrom;
    public $priceTo;
}

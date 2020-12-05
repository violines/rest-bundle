<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Request;

use Violines\RestBundle\HttpApi\HttpApi;

/**
 * @HttpApi(requestInfoSource=HttpApi::QUERY_STRING)
 */
final class QueryStringHttpApi
{
    public $priceFrom;
    public $priceTo;
}

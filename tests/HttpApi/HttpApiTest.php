<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\HttpApi;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\HttpApi\HttpApi;

/**
 * @covers \TerryApiBundle\HttpApi\HttpApi
 */
class HttpApiTest extends TestCase
{
    /**
     * @dataProvider providerShouldGenerateFromAssocArray
     */
    public function testShouldGenerateFromAssocArray($expected, $givenAnnotationArray)
    {
        $httpApi = new HttpApi($givenAnnotationArray);

        $this->assertInstanceOf(HttpApi::class, $httpApi);
        $this->assertEquals($expected, $httpApi->getRequestInfoSource());
    }

    public function providerShouldGenerateFromAssocArray(): array
    {
        return [
            [
                'body',
                []
            ],
            [
                'body',
                ['requestInfoSource' => 'body']
            ]
        ];
    }

    public function testShouldGenerateDefault()
    {
        $httpApi = new HttpApi();

        $this->assertInstanceOf(HttpApi::class, $httpApi);
        $this->assertEquals('body', $httpApi->getRequestInfoSource());
    }

    /**
     * @dataProvider providerShouldGenerateFromStringParams
     */
    public function testShouldGenerateFromStringParams($expected, $requestInfoSource)
    {
        $httpApi = new HttpApi(null, $requestInfoSource);
 
        $this->assertInstanceOf(HttpApi::class, $httpApi);
        $this->assertEquals($expected, $httpApi->getRequestInfoSource());
    }
 
    public function providerShouldGenerateFromStringParams(): array
    {
        return [
             [
                 'body',
                 'body'
             ],
             [
                 'query_string',
                 'query_string'
             ]
         ];
    }
}

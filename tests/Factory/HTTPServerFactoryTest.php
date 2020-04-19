<?php

declare(strict_types=1);

namespace TerryApi\Tests\Factory;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\Factory\HTTPServerFactory;

class HTTPServerFactoryTest extends TestCase
{
    /**
     * @dataProvider providerShouldCreateHTTPServer
     */
    public function testShouldCreateHTTPServer(array $config, $expetedFormats, $expectedFormatDefault)
    {
        $httpServerFactory = new HTTPServerFactory($config);
        $httpServer = $httpServerFactory->fromConfig();

        $this->assertEquals($expetedFormats, $httpServer->formatSerializerMap());
        $this->assertEquals($expectedFormatDefault, $httpServer->formatDefault());
    }

    public function providerShouldCreateHTTPServer()
    {
        return [
            [
                [
                    'formats' => [
                        'json' => [
                            'application/json',
                            'application/json+ld',
                        ],
                        'xml' => [
                            'application/xml'
                        ]
                    ],
                    'format_default' => 'application/xml',
                ],
                [
                    'application/json' => 'json',
                    'application/json+ld' => 'json',
                    'application/xml' => 'xml'
                ],
                'application/xml'
            ],
            [
                [
                    'formats' => [
                        'json' => [],
                        'xml' => []
                    ],
                    'format_default' => '',
                ],
                [
                    'application/json' => 'json'
                ],
                'application/json'
            ]
        ];
    }
}

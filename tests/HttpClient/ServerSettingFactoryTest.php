<?php

declare(strict_types=1);

namespace TerryApi\Tests\Factory;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\HttpClient\ServerSettingsFactory;

class ServerSettingsFactoryTest extends TestCase
{
    /**
     * @dataProvider providerShouldCreateServerSettings
     */
    public function testShouldCreateServerSettings(array $config, $expectedFormatSerializerMap, $expectedFormatDefault)
    {
        $serverSettingsFactory = new ServerSettingsFactory($config);
        $serverSettings = $serverSettingsFactory->fromConfig();

        $this->assertEquals($expectedFormatSerializerMap, $serverSettings->getFormatSerializerMap());
        $this->assertEquals($expectedFormatDefault, $serverSettings->getFormatDefault());
    }

    public function providerShouldCreateServerSettings()
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

<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use TerryApiBundle\DependencyInjection\Configuration;

class ConfigurationTest extends TestCase
{
    /**
     * @dataProvider providerShouldCheckConfiguration
     */
    public function testShouldCheckConfiguration(array $input, array $expected)
    {
        $configuration = new Configuration();

        $node = $configuration
            ->getConfigTreeBuilder()
            ->buildTree();

        $finalized = $node->finalize($node->normalize($input));

        $this->assertEquals($expected, $finalized);
    }

    public function providerShouldCheckConfiguration()
    {
        return [
            [
                [],
                [
                    'http_server' => [
                        'formats' => [
                            'json' => [],
                            'xml' => []
                        ],
                        'format_default' => '',
                    ],
                ],
            ],
            [
                [
                    'http_server' => [
                        'formats' => [
                            'json' => [
                                'application/json',
                                'application/json+ld'
                            ],
                            'xml' => [
                                'application/xml',
                                'text/html'
                            ]
                        ],
                        'format_default' => 'application/json',
                    ]
                ],
                [
                    'http_server' => [
                        'formats' => [
                            'json' => [
                                'application/json',
                                'application/json+ld'
                            ],
                            'xml' => [
                                'application/xml',
                                'text/html'
                            ]
                        ],
                        'format_default' => 'application/json',
                    ],
                ],
            ],
        ];
    }
}

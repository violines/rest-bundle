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
    public function testShouldCheckConfiguration(array $input, array $expected): void
    {
        $configuration = new Configuration();

        $node = $configuration
            ->getConfigTreeBuilder()
            ->buildTree();

        $finalized = $node->finalize($node->normalize($input));

        $this->assertEquals($expected, $finalized);
    }

    public function providerShouldCheckConfiguration(): array
    {
        return [
            [
                [],
                [
                    'serialize' => [
                        'formats' => [
                            'json' => ['application/json'],
                            'xml' => ['application/xml']
                        ],
                        'format_default' => 'application/json',
                    ],
                ],
            ],
            [
                [
                    'serialize' => [
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
                    'serialize' => [
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

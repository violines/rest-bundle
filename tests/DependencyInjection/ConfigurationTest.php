<?php

declare(strict_types=1);

namespace TerryApi\Tests\DependencyInjection;

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
                    'event_listener' => [
                        'array_response_listener' => [
                            'enable' => true,
                        ],
                        'http_error_listener' => [
                            'enable' => true,
                        ],
                        'response_transform_listener' => [
                            'enable' => true,
                        ],
                    ],
                    'argument_value_resolver' => [
                        'abstract_http_client_resolver' => [
                            'enable' => true,
                        ],
                        'request_single_struct_resolver' => [
                            'enable' => true,
                        ],
                        'request_array_of_structs_resolver' => [
                            'enable' => true,
                        ],
                    ],
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
                    'event_listener' => [
                        'array_response_listener' => [
                            'enable' => true,
                        ],
                        'http_error_listener' => [
                            'enable' => true,
                        ],
                        'response_transform_listener' => [
                            'enable' => true,
                        ],
                    ],
                    'argument_value_resolver' => [
                        'abstract_http_client_resolver' => [
                            'enable' => true,
                        ],
                        'request_single_struct_resolver' => [
                            'enable' => true,
                        ],
                        'request_array_of_structs_resolver' => [
                            'enable' => true,
                        ],
                    ],
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
            [
                [
                    'event_listener' => [
                        'array_response_listener' => [
                            'enable' => false,
                        ],
                        'http_error_listener' => [
                            'enable' => false,
                        ],
                        'response_transform_listener' => [
                            'enable' => false,
                        ],
                    ],
                    'argument_value_resolver' => [
                        'abstract_http_client_resolver' => [
                            'enable' => false,
                        ],
                        'request_single_struct_resolver' => [
                            'enable' => false,
                        ],
                        'request_array_of_structs_resolver' => [
                            'enable' => false,
                        ],
                    ]
                ],
                [
                    'event_listener' => [
                        'array_response_listener' => [
                            'enable' => false,
                        ],
                        'http_error_listener' => [
                            'enable' => false,
                        ],
                        'response_transform_listener' => [
                            'enable' => false,
                        ],
                    ],
                    'argument_value_resolver' => [
                        'abstract_http_client_resolver' => [
                            'enable' => false,
                        ],
                        'request_single_struct_resolver' => [
                            'enable' => false,
                        ],
                        'request_array_of_structs_resolver' => [
                            'enable' => false,
                        ],
                    ],
                    'http_server' => [
                        'formats' => [
                            'json' => [],
                            'xml' => []
                        ],
                        'format_default' => '',
                    ],
                ],
            ],
        ];
    }
}

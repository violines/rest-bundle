<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Violines\RestBundle\DependencyInjection\ViolinesRestExtension;

/**
 * @covers \Violines\RestBundle\DependencyInjection\ViolinesRestExtension
 */
class ViolinesRestExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @dataProvider providerForEntryPointServiceIds
     */
    public function testShouldCheckDefaultServiceLoad(string $serviceId): void
    {
        $this->load();

        $this->assertContainerBuilderHasService($serviceId);
    }

    public function providerForEntryPointServiceIds(): array
    {
        return [
            [
                'violines_rest.error.validation_exception_listener'
            ],
            [
                'violines_rest.error.error_listener'
            ],
            [
                'violines_rest.http_api.http_api_reader'
            ],
            [
                'violines_rest.negotiation.content_negotiator'
            ],
            [
                'violines_rest.response.error_response_resolver'
            ],
            [
                'violines_rest.response.response_builder'
            ],
            [
                'violines_rest.response.response_listener'
            ],
            [
                'violines_rest.response.success_response_resolver'
            ],
            [
                'violines_rest.request.body_argument_resolver'
            ],
            [
                'violines_rest.request.query_string_argument_resolver'
            ],
            [
                'violines_rest.serialize.format_mapper'
            ],
            [
                'violines_rest.serialize.serializer'
            ],
            [
                'violines_rest.validation.validator'
            ]
        ];
    }

    /**
     * @dataProvider providerShouldCheckServiceConfigurationArguments
     */
    public function testShouldCheckServiceConfigurationArguments(string $serviceId, int $argNo, $expected): void
    {
        $this->load();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument($serviceId, $argNo, $expected);
    }

    public function providerShouldCheckServiceConfigurationArguments(): array
    {
        return [
            [
                'violines_rest.negotiation.content_negotiator',
                0,
                [
                    'json' => ['application/json'],
                    'xml' => ['application/xml']
                ]
            ],
            [
                'violines_rest.negotiation.content_negotiator',
                1,
                'application/json',
            ],
            [
                'violines_rest.serialize.format_mapper',
                0,
                [
                    'json' => ['application/json'],
                    'xml' => ['application/xml']
                ]
            ]
        ];
    }

    protected function getContainerExtensions(): array
    {
        return [new ViolinesRestExtension()];
    }
}

<?php

declare(strict_types=1);

namespace TerryApi\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use TerryApiBundle\DependencyInjection\TerryApiExtension;

class TerryApiExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @dataProvider providerForEntryPointServiceIds
     */
    public function testShouldCheckDefaultServiceLoad(string $serviceId)
    {
        $this->load();

        $this->assertContainerBuilderHasService($serviceId);
    }

    /**
     * @dataProvider providerForEntryPointServiceIds
     */
    public function testShouldCheckServicesNotLoaded(string $serviceId)
    {
        $parts = explode('.', $serviceId);
        $definition[$parts[1]][$parts[2]]['enable'] = false;

        $this->load($definition);

        $this->assertContainerBuilderNotHasService($serviceId);
    }

    public function providerForEntryPointServiceIds()
    {
        return [
            [
                'terry_api.event_listener.array_response_listener'
            ],
            [
                'terry_api.event_listener.http_error_listener'
            ],
            [
                'terry_api.event_listener.object_response_listener'
            ],
            [
                'terry_api.argument_value_resolver.abstract_http_client_resolver'
            ],
            [
                'terry_api.argument_value_resolver.single_object_resolver'
            ],
            [
                'terry_api.argument_value_resolver.objects_array_resolver'
            ]
        ];
    }

    /**
     * @dataProvider providerForEntryPointServiceIds
     */
    public function testCreateHTTPServerDefinition()
    {
        $this->load();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'terry_api.factory.http_server_factory',
            0,
            [
                'formats' => [
                    'json' => [],
                    'xml' => []
                ],
                'format_default' => '',
            ]
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new TerryApiExtension()];
    }
}

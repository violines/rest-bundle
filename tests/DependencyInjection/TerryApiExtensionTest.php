<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\DependencyInjection;

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

    public function providerForEntryPointServiceIds()
    {
        return [
            [
                'terry_api.error.validation_exception_listener'
            ],
            [
                'terry_api.error.error_listener'
            ],
            [
                'terry_api.http_api.http_api_reader'
            ],
            [
                'terry_api.http_client.http_client_factory'
            ],
            [
                'terry_api.http_client.server_settings_factory'
            ],
            [
                'terry_api.response.response_builder'
            ],
            [
                'terry_api.response.response_listener'
            ],
            [
                'terry_api.request.http_api_argument_resolver'
            ],
            [
                'terry_api.serialize.serializer'
            ]
        ];
    }

    public function testCreateServerSettingsFactoryDefinition()
    {
        $this->load();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'terry_api.http_client.server_settings_factory',
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

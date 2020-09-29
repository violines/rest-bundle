<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Rector\Renaming\Rector\Name\RenameClassRector;

return function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
};

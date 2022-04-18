<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $defaults = $services->defaults();
    $defaults
        ->private()
        ->autowire();

    $services
        ->load('RM\\Bundle\\JwtSecurityBundle\\EventListener\\', '../src/EventListener/*')
            ->tag('kernel.event_listener')
    ;
};

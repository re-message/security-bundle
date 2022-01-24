<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->defaults()
            ->private()
            ->autowire()

        ->load('RM\\Bundle\\JwtSecurityBundle\\EventListener\\', '../src/EventListener/*')
            ->tag('kernel.event_listener')
    ;
};

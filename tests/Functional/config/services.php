<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $parameters = $container->parameters();

    $parameters->set('env(JWT_PUBLIC_KEY)', '%kernel.project_dir%/config/jwt/public.pem');
    $parameters->set('env(JWT_PRIVATE_KEY)', '%kernel.project_dir%/config/jwt/private.pem');
};

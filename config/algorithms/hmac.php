<?php

use RM\Standard\Jwt\Algorithm\Signature\HMAC\HS256;
use RM\Standard\Jwt\Algorithm\Signature\HMAC\HS3256;
use RM\Standard\Jwt\Algorithm\Signature\HMAC\HS3512;
use RM\Standard\Jwt\Algorithm\Signature\HMAC\HS512;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->defaults()->public()

        ->set(HS256::class)
        ->set(HS512::class)
        ->set(HS3256::class)
        ->set(HS3512::class)
    ;
};

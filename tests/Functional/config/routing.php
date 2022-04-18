<?php

use RM\Bundle\JwtSecurityBundle\Tests\Functional\Controller\SecuredController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes) {
    $routes->add('secured', '/secured')
        ->controller(SecuredController::class)
        ->methods(['GET'])
    ;
};

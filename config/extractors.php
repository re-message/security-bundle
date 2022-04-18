<?php

use RM\Bundle\JwtSecurityBundle\Extractor\AuthorizationHeaderTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Extractor\BodyParameterTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Extractor\ChainTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Extractor\QueryParameterTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Extractor\TokenExtractorInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $defaults = $services->defaults();
    $defaults
        ->private()
        ->autowire()
        ->autoconfigure();

    $services
        ->set(ChainTokenExtractor::class)
        ->public();

    $services
        ->alias(TokenExtractorInterface::class, ChainTokenExtractor::class)
    ;

    $services
        ->set(AuthorizationHeaderTokenExtractor::class)
        ->set(QueryParameterTokenExtractor::class)
        ->set(BodyParameterTokenExtractor::class)
    ;
};

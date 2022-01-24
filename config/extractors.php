<?php

use RM\Bundle\JwtSecurityBundle\Extractor\AuthorizationHeaderTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Extractor\BodyParameterTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Extractor\ChainTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Extractor\QueryParameterTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Extractor\TokenExtractorInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->defaults()
            ->private()
            ->autowire()
            ->autoconfigure()

        ->set(ChainTokenExtractor::class)
            ->public()
        ->alias(TokenExtractorInterface::class, ChainTokenExtractor::class)

        ->set(AuthorizationHeaderTokenExtractor::class)
        ->set(QueryParameterTokenExtractor::class)
        ->set(BodyParameterTokenExtractor::class)
    ;
};

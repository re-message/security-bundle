<?php
/*
 * This file is a part of Relations Messenger Security Bundle.
 * This package is a part of Relations Messenger.
 *
 * @link      https://github.com/relmsg/security-bundle
 * @link      https://dev.relmsg.ru/packages/security-bundle
 * @copyright Copyright (c) 2018-2022 Relations Messenger
 * @author    Oleg Kozlov <h1karo@relmsg.ru>
 * @license   Apache License 2.0
 * @license   https://legal.relmsg.ru/licenses/security-bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use RM\Bundle\JwtSecurityBundle\Extractor\AuthorizationHeaderTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Extractor\BodyParameterTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Extractor\ChainTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Extractor\QueryParameterTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Extractor\TokenExtractorInterface;
use RM\Bundle\JwtSecurityBundle\JwtSecurityBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $defaults = $services->defaults();
    $defaults
        ->private()
        ->autowire()
        ->autoconfigure()
    ;

    $services
        ->instanceof(TokenExtractorInterface::class)
        ->tag(JwtSecurityBundle::TAG_TOKEN_EXTRACTOR)
    ;

    $services
        ->set(ChainTokenExtractor::class)
    ;

    $services
        ->alias(TokenExtractorInterface::class, ChainTokenExtractor::class)
        ->public()
    ;

    $services
        ->set(AuthorizationHeaderTokenExtractor::class)
        ->set(QueryParameterTokenExtractor::class)
        ->set(BodyParameterTokenExtractor::class)
    ;
};

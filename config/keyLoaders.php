<?php
/*
 * This file is a part of Re Message Security Bundle.
 * This package is a part of Re Message.
 *
 * @link      https://github.com/re-message/security-bundle
 * @link      https://dev.remessage.ru/packages/security-bundle
 * @copyright Copyright (c) 2018-2022 Re Message
 * @author    Oleg Kozlov <h1karo@remessage.ru>
 * @license   Apache License 2.0
 * @license   https://legal.remessage.ru/licenses/security-bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use RM\Bundle\JwtSecurityBundle\JwtSecurityBundle;
use RM\Standard\Jwt\Key\Loader\DelegatingKeyLoader;
use RM\Standard\Jwt\Key\Loader\FileKeyLoader;
use RM\Standard\Jwt\Key\Loader\KeyLoaderInterface;
use RM\Standard\Jwt\Key\Loader\ResourceLoader;
use RM\Standard\Jwt\Key\Loader\ResourceLoaderInterface;
use RM\Standard\Jwt\Key\Loader\UrlKeyLoader;
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
        ->set(ResourceLoader::class)
    ;

    $services
        ->alias(ResourceLoaderInterface::class, ResourceLoader::class)
        ->public()
    ;

    $services
        ->instanceof(KeyLoaderInterface::class)
        ->tag(JwtSecurityBundle::TAG_KEY_LOADER)
    ;

    $services
        ->set(DelegatingKeyLoader::class)
        ->set(UrlKeyLoader::class)
        ->set(FileKeyLoader::class)
    ;

    $services
        ->alias(KeyLoaderInterface::class, DelegatingKeyLoader::class)
        ->public()
    ;
};

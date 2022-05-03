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
use RM\Standard\Jwt\Key\Factory\KeyFactoryInterface;
use RM\Standard\Jwt\Key\Factory\OctetKeyFactory;
use RM\Standard\Jwt\Key\Loader\DelegatingKeyLoader;
use RM\Standard\Jwt\Key\Loader\FileKeyLoader;
use RM\Standard\Jwt\Key\Loader\KeyLoaderInterface;
use RM\Standard\Jwt\Key\Loader\ResourceLoader;
use RM\Standard\Jwt\Key\Loader\ResourceLoaderInterface;
use RM\Standard\Jwt\Key\Loader\UrlKeyLoader;
use RM\Standard\Jwt\Key\Parameter\Factory\ParameterFactory;
use RM\Standard\Jwt\Key\Parameter\Factory\ParameterFactoryInterface;
use RM\Standard\Jwt\Key\Resolver\KeyResolverInterface;
use RM\Standard\Jwt\Key\Resolver\StorageKeyResolver;
use RM\Standard\Jwt\Key\Set\KeySetSerializer;
use RM\Standard\Jwt\Key\Set\KeySetSerializerInterface;
use RM\Standard\Jwt\Key\Storage\KeyStorageInterface;
use RM\Standard\Jwt\Key\Storage\LoadableKeyStorage;
use RM\Standard\Jwt\Key\Storage\RuntimeKeyStorage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $defaults = $services->defaults();
    $defaults
        ->private()
        ->autowire()
        ->autoconfigure()
    ;

    // key parameter factory
    $services->set(ParameterFactory::class);
    $services
        ->alias(ParameterFactoryInterface::class, ParameterFactory::class)
        ->public()
    ;

    // key factory
    $services->set(OctetKeyFactory::class);
    $services
        ->alias(KeyFactoryInterface::class, OctetKeyFactory::class)
        ->public()
    ;

    // key set serializer
    $services->set(KeySetSerializer::class);
    $services
        ->alias(KeySetSerializerInterface::class, KeySetSerializer::class)
        ->public()
    ;

    // resource loader
    $services
        ->set(ResourceLoader::class)
    ;
    $services
        ->alias(ResourceLoaderInterface::class, ResourceLoader::class)
        ->public()
    ;

    // key loaders
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

    // key storages
    $services->set(RuntimeKeyStorage::class);
    $services->set(LoadableKeyStorage::class)
        ->decorate(KeyStorageInterface::class)
    ;

    $services
        ->alias(KeyStorageInterface::class, RuntimeKeyStorage::class)
        ->public()
    ;

    // key resolvers
    $services->set(StorageKeyResolver::class);
    $services
        ->alias(KeyResolverInterface::class, StorageKeyResolver::class)
        ->public()
    ;
};

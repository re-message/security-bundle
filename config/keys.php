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

use RM\Standard\Jwt\Key\Resolver\KeyResolverInterface;
use RM\Standard\Jwt\Key\Resolver\StorageKeyResolver;
use RM\Standard\Jwt\Key\Set\KeySetSerializer;
use RM\Standard\Jwt\Key\Set\KeySetSerializerInterface;
use RM\Standard\Jwt\Key\Storage\KeyStorageInterface;
use RM\Standard\Jwt\Key\Storage\LoadableKeyStorage;
use RM\Standard\Jwt\Key\Storage\RuntimeKeyStorage;
use RM\Standard\Jwt\Key\Storage\ThumbprintKeyStorage;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $defaults = $services->defaults();
    $defaults
        ->private()
        ->autowire()
        ->autoconfigure()
    ;

    $container->import('keys/factories.php');
    $container->import('keys/loaders.php');
    $container->import('keys/public.php');
    $container->import('keys/thumbprint.php');

    // key set serializer
    $services->set(KeySetSerializer::class);
    $services
        ->alias(KeySetSerializerInterface::class, KeySetSerializer::class)
        ->public()
    ;

    // key storages
    $services->set(RuntimeKeyStorage::class);
    $services->set(LoadableKeyStorage::class)
        ->decorate(KeyStorageInterface::class)
    ;
    $services->set(ThumbprintKeyStorage::class)
        ->decorate(KeyStorageInterface::class, priority: 100)
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

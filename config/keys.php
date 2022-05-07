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

use RM\Standard\Jwt\Key\Factory\OctetKeyFactory;
use RM\Standard\Jwt\Key\Resolver\KeyResolverInterface;
use RM\Standard\Jwt\Key\Resolver\StorageKeyResolver;
use RM\Standard\Jwt\Key\Set\KeySetSerializer;
use RM\Standard\Jwt\Key\Set\KeySetSerializerInterface;
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
    $container->import('keys/storages.php');
    $container->import('keys/public.php');
    $container->import('keys/thumbprint.php');
    $container->import('keys/phpseclib.php');

    // key set serializer
    $services->set(KeySetSerializer::class);
    $services
        ->alias(KeySetSerializerInterface::class, KeySetSerializer::class)
        ->public()
    ;

    // key resolvers
    $services->set(StorageKeyResolver::class);
    $services
        ->alias(KeyResolverInterface::class, StorageKeyResolver::class)
        ->public()
    ;

    if (class_exists(OctetKeyFactory::class)) {
        $container->import('keys/octet.php');
    }
};

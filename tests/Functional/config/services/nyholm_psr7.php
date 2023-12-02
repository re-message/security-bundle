<?php
/*
 * This file is a part of Re Message Security Bundle.
 * This package is a part of Re Message.
 *
 * @link      https://github.com/re-message/security-bundle
 * @link      https://dev.remessage.ru/packages/security-bundle
 * @copyright Copyright (c) 2018-2023 Re Message
 * @author    Oleg Kozlov <h1karo@remessage.ru>
 * @license   Apache License 2.0
 * @license   https://legal.remessage.ru/licenses/security-bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $psr7FactoryId = 'nyholm.psr7.psr17_factory';
    $services->set($psr7FactoryId, Psr17Factory::class);

    $services->alias(RequestFactoryInterface::class, $psr7FactoryId);
    $services->alias(ResponseFactoryInterface::class, $psr7FactoryId);
    $services->alias(ServerRequestFactoryInterface::class, $psr7FactoryId);
    $services->alias(StreamFactoryInterface::class, $psr7FactoryId);
    $services->alias(UploadedFileFactoryInterface::class, $psr7FactoryId);
    $services->alias(UriFactoryInterface::class, $psr7FactoryId);
};

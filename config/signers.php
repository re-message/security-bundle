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

use RM\Standard\Jwt\Signature\EventfulSigner;
use RM\Standard\Jwt\Signature\GeneratedSigner;
use RM\Standard\Jwt\Signature\LoggableSigner;
use RM\Standard\Jwt\Signature\Signer;
use RM\Standard\Jwt\Signature\SignerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $defaults = $services->defaults();
    $defaults
        ->private()
        ->autowire()
        ->autoconfigure()
    ;

    $services->set(Signer::class);

    $services->set(GeneratedSigner::class)
        ->decorate(SignerInterface::class, priority: 100)
    ;

    $services->set(EventfulSigner::class)
        ->decorate(SignerInterface::class, priority: -50)
    ;

    $services->set(LoggableSigner::class)
        ->decorate(SignerInterface::class, priority: -100)
    ;

    $services
        ->alias(SignerInterface::class, Signer::class)
        ->public()
    ;
};

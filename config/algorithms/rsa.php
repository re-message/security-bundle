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

use RM\Standard\Jwt\Algorithm\Signature\RSA\PS256;
use RM\Standard\Jwt\Algorithm\Signature\RSA\PS512;
use RM\Standard\Jwt\Algorithm\Signature\RSA\RS256;
use RM\Standard\Jwt\Algorithm\Signature\RSA\RS512;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $defaults = $services->defaults();
    $defaults
        ->public()
        ->autowire()
        ->autoconfigure()
    ;

    $services
        ->set(RS256::class)
        ->set(RS512::class)
        ->set(PS256::class)
        ->set(PS512::class)
    ;
};

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

use RM\Bundle\JwtSecurityBundle\JwtSecurityBundle;
use RM\Standard\Jwt\Validator\ChainPropertyValidator;
use RM\Standard\Jwt\Validator\ChainValidator;
use RM\Standard\Jwt\Validator\Property\ExpirationValidator;
use RM\Standard\Jwt\Validator\Property\IdentifierValidator;
use RM\Standard\Jwt\Validator\Property\IssuedAtValidator;
use RM\Standard\Jwt\Validator\Property\IssuerValidator;
use RM\Standard\Jwt\Validator\Property\NotBeforeValidator;
use RM\Standard\Jwt\Validator\Property\PropertyValidatorInterface;
use RM\Standard\Jwt\Validator\SignatureValidator;
use RM\Standard\Jwt\Validator\ValidatorInterface;
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
        ->instanceof(ValidatorInterface::class)
        ->tag(JwtSecurityBundle::TAG_TOKEN_VALIDATOR)
    ;

    $services
        ->alias(ValidatorInterface::class, ChainValidator::class)
        ->public()
    ;

    $services
        ->set(ChainValidator::class)
        ->set(SignatureValidator::class)
        ->set(ChainPropertyValidator::class)
    ;

    $services
        ->instanceof(PropertyValidatorInterface::class)
        ->tag(JwtSecurityBundle::TAG_PROPERTY_VALIDATOR)
    ;

    $services
        ->set(ExpirationValidator::class)
        ->set(IdentifierValidator::class)
        ->set(IssuedAtValidator::class)
        ->set(IssuerValidator::class)
        ->set(NotBeforeValidator::class)
    ;
};

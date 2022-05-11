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

use RM\Bundle\JwtSecurityBundle\Extractor\QueryParameterTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Key\ResourceType;
use RM\Standard\Jwt\Generator\ExpirationGenerator;
use RM\Standard\Jwt\Generator\IdentifierGenerator;
use RM\Standard\Jwt\Generator\IssuedAtGenerator;
use RM\Standard\Jwt\Generator\IssuerGenerator;
use RM\Standard\Jwt\Identifier\UniqIdGenerator;
use RM\Standard\Jwt\Storage\RuntimeTokenStorage;
use RM\Standard\Jwt\Validator\Property\ExpirationValidator;
use RM\Standard\Jwt\Validator\Property\IdentifierValidator;
use RM\Standard\Jwt\Validator\Property\IssuedAtValidator;
use RM\Standard\Jwt\Validator\Property\IssuerValidator;
use RM\Standard\Jwt\Validator\Property\NotBeforeValidator;
use Symfony\Config\JwtSecurityConfig;

return static function (JwtSecurityConfig $config): void {
    $config->tokenStorage()
        ->class(RuntimeTokenStorage::class)
    ;

    $config->identifierGenerator()
        ->class(UniqIdGenerator::class)
        ->arguments(['prefix' => 'jwt_'])
    ;

    $config->propertyGenerator()
        ->class(IdentifierGenerator::class)
    ;

    $config->propertyGenerator()
        ->class(ExpirationGenerator::class)
    ;

    $config->propertyGenerator()
        ->class(IssuedAtGenerator::class)
    ;

    $config->propertyGenerator()
        ->class(IssuerGenerator::class)
        ->arguments(['issuer' => 'remessage/security-bundle'])
    ;

    $config->propertyGenerator()
        ->class('NotBeforeGenerator')
    ;

    $config->propertyValidator()
        ->class(IdentifierValidator::class)
    ;

    $config->propertyValidator()
        ->class(ExpirationValidator::class)
        ->arguments(['leeway' => 30])
    ;

    $config->propertyValidator()
        ->class(IssuedAtValidator::class)
        ->arguments(['leeway' => 30])
    ;

    $config->propertyValidator()
        ->class(NotBeforeValidator::class)
    ;

    $config->propertyValidator()
        ->class(IssuerValidator::class)
        ->arguments(['issuers' => ['remessage/security-bundle']])
    ;

    $config->tokenExtractor()
        ->class(QueryParameterTokenExtractor::class)
    ;

    $keys = $config->keys();

    $keys->resource()
        ->type(ResourceType::FILE->value)
        ->set('path', '%kernel.project_dir%/config/jwt/keys.json')
    ;
};

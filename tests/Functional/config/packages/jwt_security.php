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

use RM\Bundle\JwtSecurityBundle\Extractor\QueryParameterTokenExtractor;
use RM\Standard\Jwt\Storage\RedisTokenStorage;
use RM\Standard\Jwt\Validator\Property\ExpirationValidator;
use RM\Standard\Jwt\Validator\Property\IdentifierValidator;
use RM\Standard\Jwt\Validator\Property\IssuedAtValidator;
use RM\Standard\Jwt\Validator\Property\IssuerValidator;
use RM\Standard\Jwt\Validator\Property\NotBeforeValidator;
use Symfony\Config\JwtSecurityConfig;

return static function (JwtSecurityConfig $config) {
    $config->tokenStorage()
        ->class(RedisTokenStorage::class)
        ->arguments(['dsn' => 'redis://127.0.0.1'])
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
        ->arguments(['issuers' => ['relmsg/security-bundle']])
    ;

    $config->tokenExtractor()
        ->class(QueryParameterTokenExtractor::class)
    ;
};

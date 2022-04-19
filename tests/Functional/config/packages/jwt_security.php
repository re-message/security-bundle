<?php

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
};

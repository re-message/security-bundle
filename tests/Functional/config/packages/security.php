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

use RM\Bundle\JwtSecurityBundle\Security\JwtAuthenticator;
use RM\Bundle\JwtSecurityBundle\Tests\Functional\Provider\SubjectProvider;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $config): void {
    $config->provider('subject')
        ->id(SubjectProvider::class)
    ;

    $config->firewall('default')
        ->pattern('^/')
        ->stateless(true)
        ->customAuthenticators([JwtAuthenticator::class])
    ;
};

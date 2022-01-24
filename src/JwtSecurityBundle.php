<?php
/*
 * This file is a part of Relations Messenger Security Bundle.
 * This package is a part of Relations Messenger.
 *
 * @link      https://github.com/relmsg/security-bundle
 * @link      https://dev.relmsg.ru/packages/security-bundle
 * @copyright Copyright (c) 2018-2022 Relations Messenger
 * @author    h1karo <h1karo@outlook.com>
 * @license   Apache License 2.0
 * @license   https://legal.relmsg.ru/licenses/security-bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RM\Bundle\JwtSecurityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Oleg Kozlov <h1karo@outlook.com>
 */
class JwtSecurityBundle extends Bundle
{
    public const NAME = 'jwt_security';

    public const PUBLIC_KEY_PARAMETER = JwtSecurityBundle::NAME . '.public_key';
    public const PRIVATE_KEY_PARAMETER = JwtSecurityBundle::NAME . '.private_key';
}

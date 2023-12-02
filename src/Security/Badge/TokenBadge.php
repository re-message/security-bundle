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

namespace RM\Bundle\JwtSecurityBundle\Security\Badge;

use RM\Standard\Jwt\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\BadgeInterface;

class TokenBadge implements BadgeInterface
{
    public function __construct(
        private readonly TokenInterface $token
    ) {
    }

    public function getToken(): TokenInterface
    {
        return $this->token;
    }

    public function isResolved(): bool
    {
        return true;
    }
}

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

namespace RM\Bundle\JwtSecurityBundle\Tests\Functional\Provider;

use RM\Bundle\JwtSecurityBundle\Tests\Functional\Entity\Subject;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @internal
 */
final class SubjectProvider implements UserProviderInterface
{
    public function loadUserByIdentifier(string $identifier): Subject
    {
        return new Subject($identifier);
    }

    public function refreshUser(UserInterface $user): Subject
    {
        throw new UnsupportedUserException();
    }

    public function supportsClass(string $class): bool
    {
        return Subject::class === $class;
    }
}

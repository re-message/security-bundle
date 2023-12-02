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

namespace RM\Bundle\JwtSecurityBundle\EventListener;

use RM\Bundle\JwtSecurityBundle\Security\Badge\SubjectBadge;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

#[AsEventListener(priority: 1024)]
class SubjectProviderListener
{
    public function __construct(
        private readonly ?UserProviderInterface $userProvider = null,
    ) {
    }

    public function __invoke(CheckPassportEvent $event): void
    {
        if (null === $this->userProvider) {
            return;
        }

        $passport = $event->getPassport();
        if (!$passport->hasBadge(SubjectBadge::class)) {
            return;
        }

        /** @var SubjectBadge $badge */
        $badge = $passport->getBadge(SubjectBadge::class);
        if (null !== $badge->getUserLoader()) {
            return;
        }

        $badge->setUserLoader([$this->userProvider, 'loadUserByIdentifier']);
    }
}

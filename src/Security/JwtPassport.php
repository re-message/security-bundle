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

namespace RM\Bundle\JwtSecurityBundle\Security;

use LogicException;
use RM\Bundle\JwtSecurityBundle\Entity\AudienceInterface;
use RM\Bundle\JwtSecurityBundle\Entity\SubjectInterface;
use RM\Bundle\JwtSecurityBundle\Security\Badge\AudienceBadge;
use RM\Bundle\JwtSecurityBundle\Security\Badge\SubjectBadge;
use RM\Bundle\JwtSecurityBundle\Security\Badge\TokenBadge;
use RM\Standard\Jwt\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\BadgeInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class JwtPassport extends SelfValidatingPassport
{
    /**
     * @param BadgeInterface[] $badges
     */
    public function __construct(SubjectBadge $subjectBadge, array $badges = [])
    {
        parent::__construct($subjectBadge, $badges);
    }

    public function getSubject(): SubjectInterface
    {
        return $this->getUser();
    }

    public function getUser(): SubjectInterface
    {
        if (null === $this->user) {
            if (!$this->hasBadge(SubjectBadge::class)) {
                throw new LogicException('Cannot get the user, no SubjectBadge configured for this passport.');
            }

            /** @var SubjectBadge $badge */
            $badge = $this->getBadge(SubjectBadge::class);
            $this->user = $badge->getSubject();
        }

        return $this->user;
    }

    /**
     * @return array<int, AudienceInterface>
     */
    public function getAudiences(): array
    {
        if (!$this->hasBadge(AudienceBadge::class)) {
            return [];
        }

        /** @var AudienceBadge $audienceBadge */
        $audienceBadge = $this->getBadge(AudienceBadge::class);

        return $audienceBadge->getAudiences();
    }

    public function getToken(): TokenInterface
    {
        if (!$this->hasBadge(TokenBadge::class)) {
            throw new LogicException('Cannot get the token, no TokenBadge configured for this passport.');
        }

        /** @var TokenBadge $tokenBadge */
        $tokenBadge = $this->getBadge(TokenBadge::class);

        return $tokenBadge->getToken();
    }
}

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

namespace RM\Bundle\JwtSecurityBundle\Security;

use RM\Bundle\JwtSecurityBundle\Entity\AudienceInterface;
use RM\Bundle\JwtSecurityBundle\Entity\SubjectInterface;
use RM\Standard\Jwt\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use UnexpectedValueException;

class JwtToken extends AbstractToken
{
    /**
     * @var array<int, AudienceInterface>
     */
    private array $audiences;

    private TokenInterface $token;

    /**
     * @param SubjectInterface    $subject
     * @param AudienceInterface[] $audiences
     */
    public function __construct(SubjectInterface $subject, array $audiences, TokenInterface $token)
    {
        parent::__construct();

        $this->setUser($subject);
        $this->audiences = $audiences;
        $this->token = $token;
    }

    public function getSubject(): ?SubjectInterface
    {
        $subject = $this->getUser();
        if (null === $subject) {
            return null;
        }

        if (!$subject instanceof SubjectInterface) {
            $message = sprintf(
                'The token must return a "%s" object, "%s" given.',
                SubjectInterface::class,
                $subject::class
            );

            return throw new UnexpectedValueException($message);
        }

        return $subject;
    }

    /**
     * @return array<int, AudienceInterface>
     */
    public function getAudiences(): array
    {
        return $this->audiences;
    }

    public function getToken(): TokenInterface
    {
        return $this->token;
    }
}

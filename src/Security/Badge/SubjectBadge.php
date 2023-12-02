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

use RM\Bundle\JwtSecurityBundle\Entity\SubjectInterface;
use RM\Bundle\JwtSecurityBundle\Exception\SubjectNotFoundException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use UnexpectedValueException;

/**
 * @author Oleg Kozlov <h1karo@remessage.ru>
 */
class SubjectBadge extends UserBadge
{
    public function __construct(string $subjectId)
    {
        parent::__construct($subjectId);
    }

    public function getSubjectId(): string
    {
        return $this->getUserIdentifier();
    }

    public function getSubject(): SubjectInterface
    {
        try {
            $subject = $this->getUser();
        } catch (UserNotFoundException $e) {
            $exception = new SubjectNotFoundException(previous: $e);
            $exception->setSubjectId($this->getSubjectId());

            throw $exception;
        }

        if (!$subject instanceof SubjectInterface) {
            $message = sprintf(
                'The provider must return a "%s" object, "%s" given.',
                SubjectInterface::class,
                $subject::class
            );

            throw new UnexpectedValueException($message);
        }

        return $subject;
    }
}

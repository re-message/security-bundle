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

use Psr\EventDispatcher\EventDispatcherInterface;
use RM\Bundle\JwtSecurityBundle\Event\AuthenticationFailureEvent;
use RM\Bundle\JwtSecurityBundle\Extractor\TokenExtractorInterface;
use RM\Bundle\JwtSecurityBundle\Response\AuthenticationFailureResponse;
use RM\Bundle\JwtSecurityBundle\Security\Badge\AudienceBadge;
use RM\Bundle\JwtSecurityBundle\Security\Badge\SubjectBadge;
use RM\Bundle\JwtSecurityBundle\Security\Badge\TokenBadge;
use RM\Standard\Jwt\Exception\InvalidTokenExceptionInterface;
use RM\Standard\Jwt\Property\Payload\Audience;
use RM\Standard\Jwt\Property\Payload\Subject;
use RM\Standard\Jwt\Serializer\SignatureSerializerInterface;
use RM\Standard\Jwt\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class JwtAuthenticator implements AuthenticatorInterface
{
    public function __construct(
        private readonly TokenExtractorInterface $extractor,
        private readonly SignatureSerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function supports(Request $request): bool
    {
        return $this->extractor->supports($request);
    }

    public function authenticate(Request $request): Passport
    {
        $rawToken = $this->extractor->extract($request);
        $token = $this->serializer->deserialize($rawToken);

        try {
            if (!$this->validator->validate($token)) {
                throw new CustomUserMessageAuthenticationException('Token validation failed.');
            }
        } catch (InvalidTokenExceptionInterface $e) {
            throw new CustomUserMessageAuthenticationException('Token validation failed.', previous: $e);
        }

        $subjectClaim = $token->getPayload()->get(Subject::NAME);
        $subjectBadge = new SubjectBadge($subjectClaim->getValue());

        $badges = [];
        $badges[] = new TokenBadge($token);

        $audienceClaim = $token->getPayload()->find(Audience::NAME);
        if (null !== $audienceClaim) {
            $badges[] = new AudienceBadge($audienceClaim->getValue());
        }

        return new SelfValidatingPassport($subjectBadge, $badges);
    }

    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        /** @var SubjectBadge $subjectBadge */
        $subjectBadge = $passport->getBadge(SubjectBadge::class);
        /** @var AudienceBadge|null $audienceBadge */
        $audienceBadge = $passport->getBadge(AudienceBadge::class);
        /** @var TokenBadge $tokenBadge */
        $tokenBadge = $passport->getBadge(TokenBadge::class);

        $subject = $subjectBadge->getSubject();
        $audiences = $audienceBadge?->getAudiences() ?? [];
        $token = $tokenBadge->getToken();

        return new JwtToken($subject, $audiences, $token);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $event = new AuthenticationFailureEvent(new AuthenticationFailureResponse($exception->getMessageKey()));
        $this->eventDispatcher->dispatch($event);

        return $event->getResponse();
    }
}

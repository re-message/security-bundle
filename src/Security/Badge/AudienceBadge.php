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

use Closure;
use LogicException;
use RM\Bundle\JwtSecurityBundle\Entity\AudienceInterface;
use RM\Bundle\JwtSecurityBundle\Exception\AudienceNotFoundException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\BadgeInterface;
use UnexpectedValueException;

/**
 * @author Oleg Kozlov <h1karo@remessage.ru>
 */
class AudienceBadge implements BadgeInterface
{
    /**
     * @var array<int, AudienceInterface>|null
     */
    private ?array $audiences = null;

    /**
     * @var array<int, string>
     */
    private readonly array $audienceIds;

    private ?Closure $audienceLoader;

    public function __construct(array $audienceIds, callable $audienceLoader = null)
    {
        $this->audienceIds = $audienceIds;
        $this->audienceLoader = $audienceLoader ? $audienceLoader(...) : null;
    }

    /**
     * @return array<int, string>
     */
    public function getAudienceIds(): array
    {
        return $this->audienceIds;
    }

    public function getAudienceLoader(): ?Closure
    {
        return $this->audienceLoader;
    }

    public function setAudienceLoader(callable $audienceLoader): void
    {
        $this->audienceLoader = $audienceLoader(...);
    }

    /**
     * @return array<int, AudienceInterface>
     */
    public function getAudiences(): array
    {
        if ($this->audiences) {
            return $this->audiences;
        }

        $audiences = [];
        foreach ($this->audienceIds as $audienceId) {
            $audiences[] = $this->getAudience($audienceId);
        }

        return $this->audiences = $audiences;
    }

    protected function getAudience(string $audienceId): AudienceInterface
    {
        if (null === $this->audienceLoader) {
            throw new LogicException('No audience loader is configured');
        }

        $audience = ($this->audienceLoader)($audienceId);

        if (null === $audience) {
            $exception = new AudienceNotFoundException();
            $exception->setAudienceId($audienceId);

            throw $exception;
        }

        if (!$audience instanceof AudienceInterface) {
            $message = sprintf(
                'The provider must return a "%s" object, "%s" given.',
                AudienceInterface::class,
                $audience::class
            );

            throw new UnexpectedValueException($message);
        }

        return $audience;
    }

    public function isResolved(): bool
    {
        return true;
    }
}

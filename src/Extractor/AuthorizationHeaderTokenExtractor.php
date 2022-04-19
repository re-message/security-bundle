<?php
/*
 * This file is a part of Relations Messenger Security Bundle.
 * This package is a part of Relations Messenger.
 *
 * @link      https://github.com/relmsg/security-bundle
 * @link      https://dev.relmsg.ru/packages/security-bundle
 * @copyright Copyright (c) 2018-2022 Relations Messenger
 * @author    Oleg Kozlov <h1karo@relmsg.ru>
 * @license   Apache License 2.0
 * @license   https://legal.relmsg.ru/licenses/security-bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RM\Bundle\JwtSecurityBundle\Extractor;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Oleg Kozlov <h1karo@relmsg.ru>
 */
class AuthorizationHeaderTokenExtractor implements TokenExtractorInterface
{
    public const HEADER = 'Authorization';
    public const PREFIX = 'Bearer';

    public function __construct(
        private readonly string $name = self::HEADER,
        private readonly string $prefix = self::PREFIX,
    ) {
    }

    public function extract(Request $request): ?string
    {
        $header = $request->headers->get($this->name);
        if (null === $header) {
            return null;
        }

        if (empty($this->prefix)) {
            return $header;
        }

        $parts = explode(' ', $header);

        if (2 !== count($parts) || 0 !== strcasecmp($parts[0], $this->prefix)) {
            return null;
        }

        return $parts[1];
    }

    public function supports(Request $request): bool
    {
        return $request->headers->has($this->name);
    }
}

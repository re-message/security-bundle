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

namespace RM\Bundle\JwtSecurityBundle\Extractor;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Oleg Kozlov <h1karo@remessage.ru>
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

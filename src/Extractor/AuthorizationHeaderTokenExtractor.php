<?php
/*
 * This file is a part of Relations Messenger Security Bundle.
 * This package is a part of Relations Messenger.
 *
 * @link      https://github.com/relmsg/security-bundle
 * @link      https://dev.relmsg.ru/packages/security-bundle
 * @copyright Copyright (c) 2018-2022 Relations Messenger
 * @author    h1karo <h1karo@outlook.com>
 * @license   Apache License 2.0
 * @license   https://legal.relmsg.ru/licenses/security-bundle
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RM\Bundle\JwtSecurityBundle\Extractor;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Oleg Kozlov <h1karo@outlook.com>
 */
class AuthorizationHeaderTokenExtractor extends AbstractTokenExtractor
{
    public const HEADER = 'Authorization';
    public const PREFIX = 'Bearer';

    private string $name;
    private string $prefix;

    public function __construct(string $name = self::HEADER, string $prefix = self::PREFIX)
    {
        $this->name = $name;
        $this->prefix = $prefix;
    }

    public function extract(Request $request): ?string
    {
        if (!$request->headers->has($this->name)) {
            return null;
        }

        $header = $request->headers->get($this->name);
        if (empty($this->prefix)) {
            return $header;
        }

        $parts = explode(' ', $header);

        if (count($parts) !== 2 || strcasecmp($parts[0], $this->prefix) !== 0) {
            return null;
        }

        return $parts[1];
    }

    public function supports(Request $request): bool
    {
        return parent::supports($request) && $request->headers->has($this->name);
    }
}

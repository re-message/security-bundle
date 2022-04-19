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
class QueryParameterTokenExtractor implements TokenExtractorInterface
{
    public const QUERY_PARAMETER = 'token';

    public function __construct(
        private readonly string $parameterName = self::QUERY_PARAMETER
    ) {
    }

    public function extract(Request $request): ?string
    {
        return $request->query->get($this->parameterName);
    }

    public function supports(Request $request): bool
    {
        return $request->query->has($this->parameterName);
    }
}

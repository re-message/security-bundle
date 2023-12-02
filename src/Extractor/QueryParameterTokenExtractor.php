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

namespace RM\Bundle\JwtSecurityBundle\Extractor;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Oleg Kozlov <h1karo@remessage.ru>
 */
class QueryParameterTokenExtractor implements TokenExtractorInterface
{
    final public const string QUERY_PARAMETER = 'token';

    public function __construct(
        private readonly string $parameterName = self::QUERY_PARAMETER
    ) {}

    public function extract(Request $request): ?string
    {
        return (string) $request->query->get($this->parameterName);
    }

    public function supports(Request $request): bool
    {
        return $request->query->has($this->parameterName);
    }
}

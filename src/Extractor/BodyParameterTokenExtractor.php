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

use Override;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Oleg Kozlov <h1karo@remessage.ru>
 */
readonly class BodyParameterTokenExtractor implements TokenExtractorInterface
{
    final public const string PARAMETER = 'token';

    public function __construct(
        private string $parameterName = self::PARAMETER
    ) {}

    #[Override]
    public function extract(Request $request): ?string
    {
        $parameter = $request->request->get($this->parameterName);
        if (null === $parameter) {
            return null;
        }

        return (string) $parameter;
    }

    #[Override]
    public function supports(Request $request): bool
    {
        return $request->request->has($this->parameterName);
    }
}

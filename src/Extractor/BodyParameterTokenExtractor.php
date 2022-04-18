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
class BodyParameterTokenExtractor extends AbstractTokenExtractor
{
    public const PARAMETER = 'token';

    private string $parameterName;

    public function __construct(string $name = self::PARAMETER)
    {
        $this->parameterName = $name;
    }

    public function extract(Request $request): ?string
    {
        $parameter = $request->request->get($this->parameterName);
        if (null === $parameter) {
            return null;
        }

        return (string) $parameter;
    }

    public function supports(Request $request): bool
    {
        return parent::supports($request) && $request->request->has($this->parameterName);
    }
}

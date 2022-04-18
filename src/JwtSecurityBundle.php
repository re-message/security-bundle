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

namespace RM\Bundle\JwtSecurityBundle;

use RM\Bundle\JwtSecurityBundle\DependencyInjection\Compiler\TokenExtractorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Oleg Kozlov <h1karo@relmsg.ru>
 */
class JwtSecurityBundle extends Bundle
{
    public const NAME = 'jwt_security';

    public const PUBLIC_KEY_PARAMETER = JwtSecurityBundle::NAME . '.public_key';
    public const PRIVATE_KEY_PARAMETER = JwtSecurityBundle::NAME . '.private_key';

    public const TAG_TOKEN_EXTRACTOR = JwtSecurityBundle::NAME . '.token_extractor';
    public const TAG_TOKEN_VALIDATOR = JwtSecurityBundle::NAME . '.token_validator';

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TokenExtractorPass(self::TAG_TOKEN_EXTRACTOR));
    }
}

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

namespace RM\Bundle\JwtSecurityBundle;

use RM\Bundle\JwtSecurityBundle\DependencyInjection\Compiler\PropertyValidatorPass;
use RM\Bundle\JwtSecurityBundle\DependencyInjection\Compiler\TokenExtractorPass;
use RM\Bundle\JwtSecurityBundle\DependencyInjection\Compiler\ValidatorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Oleg Kozlov <h1karo@remessage.ru>
 */
class JwtSecurityBundle extends Bundle
{
    public const NAME = 'jwt_security';

    public const TAG_TOKEN_EXTRACTOR = JwtSecurityBundle::NAME . '.token_extractor';
    public const TAG_TOKEN_VALIDATOR = JwtSecurityBundle::NAME . '.token_validator';
    public const TAG_PROPERTY_VALIDATOR = JwtSecurityBundle::NAME . '.property_validator';

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TokenExtractorPass(self::TAG_TOKEN_EXTRACTOR));
        $container->addCompilerPass(new ValidatorPass(self::TAG_TOKEN_VALIDATOR));
        $container->addCompilerPass(new PropertyValidatorPass(self::TAG_PROPERTY_VALIDATOR));
    }
}

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

namespace RM\Bundle\JwtSecurityBundle;

use Override;
use RM\Bundle\JwtSecurityBundle\DependencyInjection\Compiler\AlgorithmPass;
use RM\Bundle\JwtSecurityBundle\DependencyInjection\Compiler\KeyFactoryPass;
use RM\Bundle\JwtSecurityBundle\DependencyInjection\Compiler\KeyGeneratorPass;
use RM\Bundle\JwtSecurityBundle\DependencyInjection\Compiler\KeyLoaderPass;
use RM\Bundle\JwtSecurityBundle\DependencyInjection\Compiler\PropertyGeneratorPass;
use RM\Bundle\JwtSecurityBundle\DependencyInjection\Compiler\PropertyValidatorPass;
use RM\Bundle\JwtSecurityBundle\DependencyInjection\Compiler\PublicKeyTransformerPass;
use RM\Bundle\JwtSecurityBundle\DependencyInjection\Compiler\SecLibTransformerPass;
use RM\Bundle\JwtSecurityBundle\DependencyInjection\Compiler\TokenExtractorPass;
use RM\Bundle\JwtSecurityBundle\DependencyInjection\Compiler\ValidatorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Oleg Kozlov <h1karo@remessage.ru>
 */
class JwtSecurityBundle extends Bundle
{
    final public const string NAME = 'jwt_security';

    final public const string SERVICE_PREFIX_RESOURCE = JwtSecurityBundle::NAME . '.resource.';

    final public const string TAG_ALGORITHM = JwtSecurityBundle::NAME . '.algorithm';
    final public const string TAG_TOKEN_EXTRACTOR = JwtSecurityBundle::NAME . '.token_extractor';
    final public const string TAG_TOKEN_VALIDATOR = JwtSecurityBundle::NAME . '.token_validator';
    final public const string TAG_PROPERTY_GENERATOR = JwtSecurityBundle::NAME . '.property_generator';
    final public const string TAG_PROPERTY_VALIDATOR = JwtSecurityBundle::NAME . '.property_validator';

    final public const string TAG_KEY_LOADER = JwtSecurityBundle::NAME . '.key_loader';
    final public const string TAG_KEY_FACTORY = JwtSecurityBundle::NAME . '.key_factory';
    final public const string TAG_KEY_GENERATOR = JwtSecurityBundle::NAME . '.key_generator';
    final public const string TAG_PUBLIC_KEY_TRANSFORMER = JwtSecurityBundle::NAME . '.public_key_transformer';
    final public const string TAG_SECLIB_TRANSFORMER = JwtSecurityBundle::NAME . '.seclib_transformer';

    #[Override]
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AlgorithmPass(self::TAG_ALGORITHM));
        $container->addCompilerPass(new TokenExtractorPass(self::TAG_TOKEN_EXTRACTOR));
        $container->addCompilerPass(new ValidatorPass(self::TAG_TOKEN_VALIDATOR));
        $container->addCompilerPass(new PropertyGeneratorPass(self::TAG_PROPERTY_GENERATOR));
        $container->addCompilerPass(new PropertyValidatorPass(self::TAG_PROPERTY_VALIDATOR));
        $container->addCompilerPass(new KeyLoaderPass(self::TAG_KEY_LOADER));
        $container->addCompilerPass(new KeyFactoryPass(self::TAG_KEY_FACTORY));
        $container->addCompilerPass(new KeyGeneratorPass(self::TAG_KEY_GENERATOR));
        $container->addCompilerPass(new PublicKeyTransformerPass(self::TAG_PUBLIC_KEY_TRANSFORMER));
        $container->addCompilerPass(new SecLibTransformerPass(self::TAG_SECLIB_TRANSFORMER));
    }
}

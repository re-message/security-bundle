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

namespace RM\Bundle\JwtSecurityBundle\DependencyInjection\Compiler;

use RM\Standard\Jwt\Validator\ChainPropertyValidator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Oleg Kozlov <h1karo@relmsg.ru>
 */
class PropertyValidatorPass implements CompilerPassInterface
{
    public function __construct(
        private readonly string $propertyValidatorTag
    ) {
    }

    /**
     * @see ChainPropertyValidator::pushValidator()
     */
    public function process(ContainerBuilder $container): void
    {
        $chainValidatorDefinition = $container->findDefinition(ChainPropertyValidator::class);
        $services = $container->findTaggedServiceIds($this->propertyValidatorTag);
        foreach ($services as $id => $tags) {
            if (ChainPropertyValidator::class === $id) {
                continue;
            }

            $validatorDefinition = new Reference($id);
            $chainValidatorDefinition->addMethodCall('pushValidator', [$validatorDefinition]);
        }
    }
}

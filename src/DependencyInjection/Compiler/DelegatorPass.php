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

namespace RM\Bundle\JwtSecurityBundle\DependencyInjection\Compiler;

use RM\Standard\Jwt\Algorithm\AlgorithmManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Oleg Kozlov <h1karo@remessage.ru>
 */
abstract class DelegatorPass implements CompilerPassInterface
{
    public function __construct(
        private readonly string $tag,
        private readonly string $delegatorClass,
        private readonly string $method,
    ) {
    }

    /**
     * @see AlgorithmManager::put()
     */
    public function process(ContainerBuilder $container): void
    {
        $delegator = $container->findDefinition($this->delegatorClass);
        $services = $container->findTaggedServiceIds($this->tag);
        foreach ($services as $id => $tags) {
            if ($this->delegatorClass === $id) {
                continue;
            }

            $reference = new Reference($id);
            $delegator->addMethodCall($this->method, [$reference]);
        }
    }
}

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

namespace RM\Bundle\JwtSecurityBundle\DependencyInjection;

use RM\Bundle\JwtSecurityBundle\JwtSecurityBundle;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Oleg Kozlov <h1karo@outlook.com>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(JwtSecurityBundle::NAME);
        $root = $treeBuilder->getRootNode();
        $root
            ->addDefaultsIfNotSet()
            ->children()
                ->append($this->getKeysNode())
            ->end();

        return $treeBuilder;
    }

    private function getKeysNode(): NodeDefinition
    {
        $builder = new TreeBuilder('keys');
        $node = $builder->getRootNode();
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('public')
                    ->defaultValue('%env(file:resolve:JWT_PUBLIC_KEY)%')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('private')
                    ->defaultValue('%env(file:resolve:JWT_PRIVATE_KEY)%')
                    ->cannotBeEmpty()
                ->end()
            ->end();
        return $node;
    }
}

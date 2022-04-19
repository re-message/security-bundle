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

namespace RM\Bundle\JwtSecurityBundle\DependencyInjection;

use RM\Bundle\JwtSecurityBundle\JwtSecurityBundle;
use RM\Standard\Jwt\Validator\Property\PropertyValidatorInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Oleg Kozlov <h1karo@relmsg.ru>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(JwtSecurityBundle::NAME);

        $root = $treeBuilder->getRootNode();
        $root->addDefaultsIfNotSet();

        $children = $root->children();

        $children->append($this->getKeysNode());

        $root->fixXmlConfig('property_validator');
        $children->append($this->getPropertyValidatorsNode());

        return $treeBuilder;
    }

    protected function getKeysNode(): NodeDefinition
    {
        $builder = new TreeBuilder('keys');

        $node = $builder->getRootNode();
        $node->addDefaultsIfNotSet();

        $children = $node->children();

        $publicKey = $children->scalarNode('public');
        $publicKey
            ->defaultValue('%env(file:resolve:JWT_PUBLIC_KEY)%')
            ->cannotBeEmpty()
        ;

        $privateKey = $children->scalarNode('private');
        $privateKey
            ->defaultValue('%env(file:resolve:JWT_PRIVATE_KEY)%')
            ->cannotBeEmpty()
        ;

        return $node;
    }

    protected function getPropertyValidatorsNode(): NodeDefinition
    {
        $builder = new TreeBuilder('property_validators');

        $node = $builder->getRootNode();
        $node->performNoDeepMerging();

        $prototype = $node->arrayPrototype();

        $children = $prototype->children();

        $class = $children->scalarNode('class');
        $class->isRequired();
        $class->cannotBeEmpty();
        $this->validateInstanceOf($class, PropertyValidatorInterface::class);

        $arguments = $children->arrayNode('arguments');
        $arguments->ignoreExtraKeys(false);

        return $node;
    }

    protected function validateInstanceOf(NodeDefinition $node, string $class): void
    {
        $closure = fn ($value): bool => is_string($value) && !is_a($value, $class, true);
        $message = sprintf('The class must implement "%s", got "%%s".', $class);

        $node->validate()
            ->ifTrue($closure)
            ->thenInvalid($message)
        ;
    }
}

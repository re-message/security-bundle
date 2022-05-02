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

namespace RM\Bundle\JwtSecurityBundle\DependencyInjection;

use RM\Bundle\JwtSecurityBundle\Extractor\AuthorizationHeaderTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Extractor\BodyParameterTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Extractor\QueryParameterTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Extractor\TokenExtractorInterface;
use RM\Bundle\JwtSecurityBundle\JwtSecurityBundle;
use RM\Bundle\JwtSecurityBundle\Key\KeyResource;
use RM\Standard\Jwt\Storage\RuntimeTokenStorage;
use RM\Standard\Jwt\Storage\TokenStorageInterface;
use RM\Standard\Jwt\Validator\Property\PropertyValidatorInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Oleg Kozlov <h1karo@remessage.ru>
 */
class Configuration implements ConfigurationInterface
{
    private const ARGUMENT_PREFIX = '$';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(JwtSecurityBundle::NAME);

        $root = $treeBuilder->getRootNode();
        $root->addDefaultsIfNotSet();

        $children = $root->children();

        $children->append($this->getKeysNode());
        $children->append($this->getTokenStorageNode());

        $root->fixXmlConfig('property_validator');
        $children->append($this->getPropertyValidatorsNode());

        $root->fixXmlConfig('token_extractor');
        $children->append($this->getTokenExtractorsNode());

        return $treeBuilder;
    }

    protected function getKeysNode(): NodeDefinition
    {
        $builder = new TreeBuilder('keys');

        $node = $builder->getRootNode();
        $node->addDefaultsIfNotSet();
        $node->fixXmlConfig('resource');

        $children = $node->children();

        $resources = $children->arrayNode('resources');
        $resources->performNoDeepMerging();

        $prototype = $resources->arrayPrototype();
        $prototype->ignoreExtraKeys(false);

        $children = $prototype->children();

        $type = $children->enumNode('type');
        $type->values(KeyResource::caseNames());
        $type->isRequired();
        $type->cannotBeEmpty();

        return $node;
    }

    protected function getTokenStorageNode(): NodeDefinition
    {
        $builder = new TreeBuilder('token_storage');

        $node = $builder->getRootNode();
        $node->addDefaultsIfNotSet();

        $children = $node->children();

        $class = $children->scalarNode('class');
        $class->defaultValue(RuntimeTokenStorage::class);
        $class->cannotBeEmpty();
        $this->validateInstanceOf($class, TokenStorageInterface::class);

        $arguments = $children->arrayNode('arguments');
        $arguments->performNoDeepMerging();
        $arguments->ignoreExtraKeys(false);
        $arguments->beforeNormalization()
            ->always(fn (array $args) => $this->prefixArguments($args))
        ;

        return $node;
    }

    protected function getPropertyValidatorsNode(): NodeDefinition
    {
        return $this->getServicesNode(
            'property_validators',
            PropertyValidatorInterface::class,
        );
    }

    protected function getTokenExtractorsNode(): ArrayNodeDefinition
    {
        $node = $this->getServicesNode(
            'token_extractors',
            TokenExtractorInterface::class,
        );

        $node->requiresAtLeastOneElement();
        $node->defaultValue(
            [
                ['class' => AuthorizationHeaderTokenExtractor::class],
                ['class' => BodyParameterTokenExtractor::class],
                ['class' => QueryParameterTokenExtractor::class],
            ]
        );

        return $node;
    }

    /**
     * @param class-string $instanceOf
     */
    protected function getServicesNode(string $name, string $instanceOf): ArrayNodeDefinition
    {
        $builder = new TreeBuilder($name);

        $node = $builder->getRootNode();
        $prototype = $node->arrayPrototype();
        $children = $prototype->children();

        $class = $children->scalarNode('class');
        $class->isRequired();
        $class->cannotBeEmpty();
        $this->validateInstanceOf($class, $instanceOf);

        $arguments = $children->arrayNode('arguments');
        $arguments->performNoDeepMerging();
        $arguments->ignoreExtraKeys(false);
        $arguments->beforeNormalization()
            ->always(fn (array $args) => $this->prefixArguments($args))
        ;

        return $node;
    }

    protected function prefixArguments(array $input): array
    {
        $prefix = self::ARGUMENT_PREFIX;
        $arguments = [];

        foreach ($input as $key => $value) {
            if (is_numeric($key) || str_starts_with($key, $prefix)) {
                $arguments[$key] = $value;

                continue;
            }

            $prefixed = $prefix . $key;
            $arguments[$prefixed] = $value;
        }

        return $arguments;
    }

    /**
     * @param class-string $class
     */
    protected function validateInstanceOf(NodeDefinition $node, string $class): void
    {
        $closure = fn (mixed $value): bool => is_string($value) && !is_a($value, $class, true);
        $classEncoded = json_encode($class);
        $message = sprintf('The class must implement %s, got %%s.', $classEncoded);

        $node->validate()
            ->ifTrue($closure)
            ->thenInvalid($message)
        ;
    }
}

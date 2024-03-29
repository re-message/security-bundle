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

namespace RM\Bundle\JwtSecurityBundle\DependencyInjection;

use Closure;
use InvalidArgumentException;
use Override;
use ReflectionClass;
use RM\Bundle\JwtSecurityBundle\Extractor\AuthorizationHeaderTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Extractor\BodyParameterTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Extractor\QueryParameterTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Extractor\TokenExtractorInterface;
use RM\Bundle\JwtSecurityBundle\JwtSecurityBundle;
use RM\Bundle\JwtSecurityBundle\Key\ResourceType;
use RM\Standard\Jwt\Format\FormatterInterface;
use RM\Standard\Jwt\Format\JsonFormatter;
use RM\Standard\Jwt\Generator\PropertyGeneratorInterface;
use RM\Standard\Jwt\Identifier\IdentifierGeneratorInterface;
use RM\Standard\Jwt\Identifier\UniqIdGenerator;
use RM\Standard\Jwt\Storage\RuntimeTokenStorage;
use RM\Standard\Jwt\Storage\TokenStorageInterface;
use RM\Standard\Jwt\Validator\Property\PropertyValidatorInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @author Oleg Kozlov <h1karo@remessage.ru>
 */
class Configuration implements ConfigurationInterface
{
    private const string ARGUMENT_PREFIX = '$';

    #[Override]
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(JwtSecurityBundle::NAME);

        $root = $treeBuilder->getRootNode();
        $root->addDefaultsIfNotSet();

        $children = $root->children();

        $children->append($this->getFormatterNode());
        $children->append($this->getKeysNode());
        $children->append($this->getTokenStorageNode());

        $root->fixXmlConfig('property_generator');
        $children->append($this->getPropertyGeneratorsNode());

        $root->fixXmlConfig('property_validator');
        $children->append($this->getPropertyValidatorsNode());

        $root->fixXmlConfig('token_extractor');
        $children->append($this->getTokenExtractorsNode());

        $children->append($this->getIdentifierGeneratorNode());

        return $treeBuilder;
    }

    protected function getFormatterNode(): NodeDefinition
    {
        $builder = new TreeBuilder('formatter');

        $node = $builder->getRootNode();
        $node->addDefaultsIfNotSet();

        $children = $node->children();

        $class = $children->scalarNode('class');
        $class->defaultValue(JsonFormatter::class);
        $class->isRequired();
        $class->cannotBeEmpty();
        $this->validateInstanceOf($class, FormatterInterface::class);

        return $node;
    }

    protected function getKeysNode(): NodeDefinition
    {
        $builder = new TreeBuilder('keys');

        $node = $builder->getRootNode();
        $node->addDefaultsIfNotSet();

        $children = $node->children();

        $children->append($this->getKeyLoaderNode());
        $children->append($this->getKeyThumbprintNode());

        $node->fixXmlConfig('resource');
        $children->append($this->getKeyResourcesNode());

        return $node;
    }

    protected function getKeyLoaderNode(): NodeDefinition
    {
        $builder = new TreeBuilder('loader');

        $node = $builder->getRootNode();
        $node->canBeDisabled();

        return $node;
    }

    protected function getKeyThumbprintNode(): NodeDefinition
    {
        $builder = new TreeBuilder('thumbprint');

        $node = $builder->getRootNode();
        $node->addDefaultsIfNotSet();

        $children = $node->children();

        $children->enumNode('algorithm')
            ->values(hash_algos())
            ->defaultValue('sha256')
            ->isRequired()
            ->cannotBeEmpty()
        ;

        return $node;
    }

    protected function getKeyResourcesNode(): NodeDefinition
    {
        $builder = new TreeBuilder('resources');

        $node = $builder->getRootNode();
        $node->performNoDeepMerging();

        $prototype = $node->arrayPrototype();
        $prototype->addDefaultsIfNotSet();
        $prototype->ignoreExtraKeys(false);

        $children = $prototype->children();

        $type = $children->enumNode('type');
        $type->values(ResourceType::caseNames());
        $type->isRequired();
        $type->cannotBeEmpty();

        $children->booleanNode('required')
            ->defaultFalse()
        ;

        $prototype->validate()
            ->always(function (array $resource) {
                $type = $resource['type'];
                $notType = static fn (string $key): bool => 'type' !== $key;
                $args = array_filter($resource, $notType, ARRAY_FILTER_USE_KEY);
                $prefixedArgs = $this->prefixArguments($args);

                return array_merge(['type' => $type], $prefixedArgs);
            })
        ;

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

    protected function getPropertyGeneratorsNode(): NodeDefinition
    {
        return $this->getServicesNode(
            'property_generators',
            PropertyGeneratorInterface::class,
        );
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

    private function getIdentifierGeneratorNode(): NodeDefinition
    {
        $builder = new TreeBuilder('identifier_generator');

        $node = $builder->getRootNode();
        $node->addDefaultsIfNotSet();

        $children = $node->children();

        $class = $children->scalarNode('class');
        $class->defaultValue(UniqIdGenerator::class);
        $class->cannotBeEmpty();
        $this->validateInstanceOf($class, IdentifierGeneratorInterface::class);

        $arguments = $children->arrayNode('arguments');
        $arguments->performNoDeepMerging();
        $arguments->ignoreExtraKeys(false);
        $arguments->beforeNormalization()
            ->always(fn (array $args) => $this->prefixArguments($args))
        ;

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
        $class->validate()
            ->always($this->isInstanceOf($instanceOf))
        ;

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

    protected function isInstanceOf(string $class): Closure
    {
        if (!class_exists($class) && !interface_exists($class)) {
            $message = sprintf('Class %s does not exist.', $class);

            throw new InvalidArgumentException($message);
        }

        $reflect = new ReflectionClass($class);
        $namespace = $reflect->getNamespaceName();

        return static function (mixed $value) use ($namespace, $class) {
            if (class_exists($value) && is_a($value, $class, true)) {
                return $value;
            }

            $namespaced = $namespace . '\\' . $value;
            if (class_exists($namespaced) && is_a($namespaced, $class, true)) {
                return $namespaced;
            }

            $type = get_debug_type($value);
            $message = sprintf('The class must implement %s, got %s.', $class, $type);

            throw new InvalidConfigurationException($message);
        };
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

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

use Exception;
use RM\Bundle\JwtSecurityBundle\JwtSecurityBundle;
use RM\Standard\Jwt\Algorithm\Signature\HMAC\HMAC;
use RM\Standard\Jwt\Storage\TokenStorageInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * @author Oleg Kozlov <h1karo@remessage.ru>
 */
class JwtSecurityExtension extends Extension
{
    /**
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $fileLocator = new FileLocator(__DIR__ . '/../../config');
        $phpLoader = new PhpFileLoader($container, $fileLocator);

        $phpLoader->load('extractors.php');
        $phpLoader->load('key_resolvers.php');
        $phpLoader->load('listeners.php');
        $phpLoader->load('validators.php');

        if (class_exists(HMAC::class)) {
            $phpLoader->load('algorithms/hmac.php');
        }

        $this->registerTokenStorage($container, $config['token_storage']);
        $this->registerPropertyValidators($container, $config['property_validators']);
        $this->registerTokenExtractors($container, $config['token_extractors']);
    }

    protected function registerTokenStorage(ContainerBuilder $container, array $config): void
    {
        $class = $config['class'];
        $arguments = $config['arguments'] ?? [];

        $this->registerService($container, $class, $arguments);

        $alias = $container->setAlias(TokenStorageInterface::class, $class);
        $alias->setPublic(true);
    }

    protected function registerPropertyValidators(ContainerBuilder $container, array $configs): void
    {
        $this->registerTaggedServices(
            $container,
            $configs,
            JwtSecurityBundle::TAG_PROPERTY_VALIDATOR,
        );
    }

    protected function registerTokenExtractors(ContainerBuilder $container, array $configs): void
    {
        $this->registerTaggedServices(
            $container,
            $configs,
            JwtSecurityBundle::TAG_TOKEN_EXTRACTOR,
        );
    }

    protected function registerTaggedServices(
        ContainerBuilder $container,
        array $configs,
        string $tag,
    ): void {
        foreach ($configs as $config) {
            $class = $config['class'];
            $arguments = $config['arguments'] ?? [];

            $definition = $this->registerService($container, $class, $arguments);
            $definition->addTag($tag);
        }
    }

    protected function registerService(
        ContainerBuilder $container,
        string $class,
        array $arguments = [],
    ): Definition {
        $definition = $container->register($class);
        $definition->setArguments($arguments);
        $definition->setAutowired(true);

        return $definition;
    }
}

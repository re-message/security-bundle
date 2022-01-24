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

use Exception;
use RM\Bundle\JwtSecurityBundle\JwtSecurityBundle;
use RM\Standard\Jwt\Algorithm\Signature\HMAC\HS256;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * @author Oleg Kozlov <h1karo@outlook.com>
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

        $phpLoader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $phpLoader->load('listeners.php');
        $phpLoader->load('extractors.php');

        if (class_exists(HS256::class)) {
            $phpLoader->load('algorithms/hmac.php');
        }

        $container->setParameter(JwtSecurityBundle::PUBLIC_KEY_PARAMETER, $config['keys']['public']);
        $container->setParameter(JwtSecurityBundle::PRIVATE_KEY_PARAMETER, $config['keys']['private']);
    }
}

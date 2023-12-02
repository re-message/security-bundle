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

namespace RM\Bundle\JwtSecurityBundle\Tests\Functional;

use Exception;
use Override;
use RM\Bundle\JwtSecurityBundle\JwtSecurityBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * @author Oleg Kozlov <h1karo@remessage.ru>
 *
 * @internal
 */
final class Kernel extends BaseKernel
{
    public function __construct(
        string $environment,
        bool $debug,
        private readonly ?string $testCase = null,
    ) {
        parent::__construct($environment, $debug);
    }

    #[Override]
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new SecurityBundle(),
            new MonologBundle(),
            new JwtSecurityBundle(),
        ];
    }

    #[Override]
    public function getProjectDir(): string
    {
        return __DIR__;
    }

    #[Override]
    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/JwtSecurityBundle/cache';
    }

    #[Override]
    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/JwtSecurityBundle/log';
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $confDir = $this->getProjectDir() . '/config';

        $loader->load($confDir . '/{packages}/*.php', 'glob');
        $loader->load($confDir . '/{services}/*.php', 'glob');

        if ($this->testCase) {
            $loader->load($confDir . '/{packages}/' . $this->testCase . '/*.php', 'glob');
            $loader->load($confDir . '/{services}/' . $this->testCase . '/*.php', 'glob');
        }
    }
}

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

use RM\Bundle\JwtSecurityBundle\Extractor\ChainTokenExtractor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Oleg Kozlov <h1karo@outlook.com>
 */
class TokenExtractorPass implements CompilerPassInterface
{
    private string $tokenExtractorTag;

    public function __construct(string $tokenExtractorTag)
    {
        $this->tokenExtractorTag = $tokenExtractorTag;
    }

    /**
     * @see ChainTokenExtractor::pushExtractor()
     */
    public function process(ContainerBuilder $container): void
    {
        $chainExtractorDefinition = $container->findDefinition(ChainTokenExtractor::class);
        $services = $container->findTaggedServiceIds($this->tokenExtractorTag);
        foreach ($services as $id => $tags) {
            if (ChainTokenExtractor::class === $id) {
                continue;
            }

            $extractorDefinition = new Reference($id);
            $chainExtractorDefinition->addMethodCall('pushExtractor', [$extractorDefinition]);
        }
    }
}

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

namespace RM\Bundle\JwtSecurityBundle\Tests\Functional\DependencyInjection;

use RM\Bundle\JwtSecurityBundle\JwtSecurityBundle;
use RM\Bundle\JwtSecurityBundle\Tests\Functional\TestCase;

class DependencyInjectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    /**
     * @dataProvider provideKeyParameters
     */
    public function testKeys(string $parameterName): void
    {
        $container = self::$kernel->getContainer();

        $hasKey = $container->hasParameter($parameterName);
        self::assertTrue($hasKey);

        $key = $container->getParameter($parameterName);
        self::assertNotEmpty($key);
    }

    public function provideKeyParameters(): iterable
    {
        yield 'public' => [JwtSecurityBundle::PUBLIC_KEY_PARAMETER];

        yield 'private' => [JwtSecurityBundle::PRIVATE_KEY_PARAMETER];
    }
}

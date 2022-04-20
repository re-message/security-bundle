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

use RM\Bundle\JwtSecurityBundle\Extractor\ChainTokenExtractor;
use RM\Bundle\JwtSecurityBundle\Extractor\TokenExtractorInterface;
use RM\Bundle\JwtSecurityBundle\Tests\Functional\TestCase;
use RM\Standard\Jwt\Storage\RedisTokenStorage;
use RM\Standard\Jwt\Storage\TokenStorageInterface;
use RM\Standard\Jwt\Validator\ChainPropertyValidator;
use RM\Standard\Jwt\Validator\ChainValidator;
use RM\Standard\Jwt\Validator\Property\PropertyValidatorInterface;
use RM\Standard\Jwt\Validator\ValidatorInterface;

/**
 * @internal
 *
 * @coversDefaultClass \RM\Bundle\JwtSecurityBundle\DependencyInjection\
 * @covers Configuration
 * @covers JwtSecurityExtension
 */
class DependencyInjectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    /**
     * @covers Configuration::getPropertyValidatorsNode
     * @covers JwtSecurityExtension::registerPropertyValidators
     */
    public function testPropertyValidators(): void
    {
        $container = self::$kernel->getContainer();

        $validator = $container->get(ValidatorInterface::class);
        self::assertInstanceOf(ChainValidator::class, $validator);

        $validators = $validator->getValidators();
        self::assertContainsOnlyInstancesOf(ValidatorInterface::class, $validators);

        $chainPropertyValidator = $this->getInstanceOf($validators, ChainPropertyValidator::class);
        $propertyValidators = $chainPropertyValidator->getValidators();
        self::assertContainsOnlyInstancesOf(PropertyValidatorInterface::class, $propertyValidators);

        self::assertCount(5, $propertyValidators);
    }

    /**
     * @covers Configuration::getTokenStorageNode
     * @covers JwtSecurityExtension::registerTokenStorage
     */
    public function testTokenStorage(): void
    {
        $container = self::$kernel->getContainer();

        $storage = $container->get(TokenStorageInterface::class);
        self::assertInstanceOf(RedisTokenStorage::class, $storage);
    }

    /**
     * @covers Configuration::getTokenExtractorsNode
     * @covers JwtSecurityExtension::registerTokenExtractors
     */
    public function testTokenExtractors(): void
    {
        $container = self::$kernel->getContainer();

        $extractor = $container->get(TokenExtractorInterface::class);
        self::assertInstanceOf(ChainTokenExtractor::class, $extractor);

        $extractors = $extractor->getExtractors();
        self::assertContainsOnlyInstancesOf(TokenExtractorInterface::class, $extractors);

        self::assertCount(1, $extractors);
    }

    /**
     * @template T
     *
     * @param class-string<T> $instance
     *
     * @return T
     */
    protected function getInstanceOf(array $array, string $instance): object
    {
        $target = $this->findInstanceOf($array, $instance);
        self::assertNotNull($target);

        return $target;
    }

    /**
     * @template T
     *
     * @param class-string<T> $instance
     *
     * @return null|T
     */
    protected function findInstanceOf(array $array, string $instance): object|null
    {
        foreach ($array as $object) {
            if (is_a($object, $instance, false)) {
                return $object;
            }
        }

        return null;
    }
}

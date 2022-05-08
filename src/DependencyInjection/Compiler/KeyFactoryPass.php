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

namespace RM\Bundle\JwtSecurityBundle\DependencyInjection\Compiler;

use RM\Standard\Jwt\Key\Factory\DelegatingKeyFactory;

/**
 * @see DelegatingKeyFactory::pushFactory()
 *
 * @author Oleg Kozlov <h1karo@remessage.ru>
 */
class KeyFactoryPass extends DelegatorPass
{
    public function __construct(string $tag)
    {
        parent::__construct($tag, DelegatingKeyFactory::class, 'pushFactory');
    }
}
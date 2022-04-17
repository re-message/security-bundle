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

namespace RM\Bundle\JwtSecurityBundle;

abstract class Switchable
{
    private bool $enabled = true;

    /**
     * Enable the service.
     */
    final public function enable(): void
    {
        $this->enabled = true;
    }

    /**
     * Disable the service.
     */
    final public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * Checks that service is enabled.
     */
    final public function isEnabled(): bool
    {
        return $this->enabled;
    }
}

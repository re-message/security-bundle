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

namespace RM\Bundle\JwtSecurityBundle\EventListener;

use RM\Standard\Jwt\Key\Loader\ResourceLoaderInterface;
use RM\Standard\Jwt\Key\Storage\KeyStorageInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[AsEventListener(event: RequestEvent::class)]
#[AsEventListener(event: ConsoleCommandEvent::class)]
class KeyLoaderListener
{
    private bool $enabled = false;

    public function __construct(
        private readonly KeyStorageInterface $storage,
        private readonly ResourceLoaderInterface $loader,
    ) {}

    public function __invoke(): void
    {
        if (!$this->enabled) {
            return;
        }

        $keys = $this->loader->load();
        $this->storage->addAll($keys);
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}

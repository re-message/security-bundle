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

namespace RM\Bundle\JwtSecurityBundle\Key;

use RM\Standard\Jwt\Key\Loader\FileKeyLoader;
use RM\Standard\Jwt\Key\Loader\UrlKeyLoader;
use RM\Standard\Jwt\Key\Resource\File;
use RM\Standard\Jwt\Key\Resource\Url;

enum ResourceType: string
{
    /**
     * @see File
     * @see FileKeyLoader
     */
    case FILE = 'file';

    /**
     * @see Url
     * @see UrlKeyLoader
     */
    case URL = 'url';

    /**
     * @return array<int, string>
     */
    public static function caseNames(): array
    {
        return array_map(
            static fn (self $resource) => $resource->value,
            static::cases(),
        );
    }
}

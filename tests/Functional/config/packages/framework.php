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

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $config) {
    $config->test(true);
    $config->secret('test');

    $config->router()
        ->utf8(true)
        ->resource('%kernel.project_dir%/config/routing.php')
    ;

    $config->session()
        ->storageFactoryId('session.storage.factory.mock_file')
    ;
};

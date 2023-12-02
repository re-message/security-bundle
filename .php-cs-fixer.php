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

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->notPath('src/Key/ResourceType.php')
    ->append([__FILE__])
    ->exclude('vendor')
;

$namespace = 'Re Message';
$projectTitle = 'Re Message Security Bundle';
$projectName = 'security-bundle';
$currentYear = date('Y');

$header = <<<EOF
    This file is a part of {$projectTitle}.
    This package is a part of {$namespace}.

    @link      https://github.com/re-message/{$projectName}
    @link      https://dev.remessage.ru/packages/{$projectName}
    @copyright Copyright (c) 2018-{$currentYear} {$namespace}
    @author    Oleg Kozlov <h1karo@remessage.ru>
    @license   Apache License 2.0
    @license   https://legal.remessage.ru/licenses/{$projectName}

    For the full copyright and license information, please view the LICENSE
    file that was distributed with this source code.
    EOF;

$config = new RM\Style\RuleSet\Config();

return $config
    ->setHeader($header)
    ->setFinder($finder)
;

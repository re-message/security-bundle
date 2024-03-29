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

namespace RM\Bundle\JwtSecurityBundle\Command;

use Override;
use RM\Standard\Jwt\Key\Parameter\Type;
use RM\Standard\Jwt\Key\Storage\KeyStorageInterface;
use RM\Standard\Jwt\Property\Header\KeyId;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'rm:jwt:keys:list', description: 'List of registered keys.')]
class KeyListCommand extends Command
{
    public function __construct(
        private readonly KeyStorageInterface $storage,
    ) {
        parent::__construct();
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $table = $io->createTable();
        $table->setStyle('box');
        $table->setHeaders(['Key type', 'Key id']);

        $types = [Type::OCTET, Type::RSA];
        foreach ($types as $type) {
            $table->addRow(new TableSeparator());

            $keys = $this->storage->findByType($type);
            foreach ($keys as $key) {
                $id = $key->get(KeyId::NAME)->getValue();
                $table->addRow([$type, $id]);
            }
        }

        $table->render();

        return 0;
    }
}

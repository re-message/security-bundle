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

use RM\Standard\Jwt\Key\Generator\KeyGeneratorInterface;
use RM\Standard\Jwt\Key\Parameter\Identifier;
use RM\Standard\Jwt\Key\Parameter\Type;
use RM\Standard\Jwt\Key\Set\KeySetSerializerInterface;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'rm:jwt:keys:generate', description: 'Generate new keys for Json Web Tokens.')]
class KeyGeneratorCommand extends Command
{
    final public const string DEFAULT_PATH = 'config/jwt/keys.json';

    final public const string MODE_SKIP = 'skip';
    final public const string MODE_APPEND = 'append';
    final public const string MODE_OVERWRITE = 'overwrite';

    public function __construct(
        private readonly KeyGeneratorInterface $generator,
        private readonly KeySetSerializerInterface $keySetSerializer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'type',
            InputArgument::REQUIRED,
            'The key type to generate',
        );

        $this->addArgument(
            'path',
            InputArgument::REQUIRED,
            'Path to the file where would be keys',
        );

        $this->addArgument(
            'mode',
            InputArgument::OPTIONAL,
            'File mode',
        );
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string|null $type */
        $type = $input->getArgument('type');
        if (null === $type) {
            $type = $io->choice(
                'The key type to generate',
                [
                    Type::OCTET => 'octet',
                    Type::RSA => 'RSA',
                ]
            );
            $input->setArgument('type', $type);
        }

        /** @var string|null $path */
        $path = $input->getArgument('path');
        if (null === $path) {
            $validator = fn (string $path): string => $this->validatePath($path);
            $path = $io->ask('Path to the keys file', self::DEFAULT_PATH, $validator);
            $input->setArgument('path', $path);
        }

        /** @var string|null $mode */
        $mode = $input->getArgument('mode');
        if (null === $mode) {
            $mode = $io->choice(
                'File mode',
                [
                    self::MODE_APPEND => 'Append key to file',
                    self::MODE_OVERWRITE => 'Overwrite file with new key',
                    self::MODE_SKIP => 'Skip if file exist',
                ],
                self::MODE_APPEND,
            );
            $input->setArgument('mode', $mode);
        }
    }

    protected function validatePath(string $path): string
    {
        if (!file_exists($path)) {
            return $path;
        }

        if (!is_readable($path)) {
            throw new RuntimeException('The file by path exists but not readable.');
        }

        if (!is_writable($path)) {
            throw new RuntimeException('The file by path exists but not writable.');
        }

        return $path;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $type = $input->getArgument('type');
        $path = $input->getArgument('path');
        $mode = $input->getArgument('mode') ?? self::MODE_APPEND;

        if (self::MODE_SKIP === $mode && file_exists($path)) {
            $io->note(sprintf('The file "%s" exists. The generation skipped.', $path));

            return 0;
        }

        $key = $this->generator->generate($type);
        $id = $key->get(Identifier::NAME)->getValue();
        $io->info(sprintf('Created key "%s" with id "%s".', $type, $id));

        $dir = dirname($path);
        if (!file_exists($dir) && !mkdir($dir, recursive: true) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        if (self::MODE_OVERWRITE === $mode) {
            $keySet = $this->keySetSerializer->serialize([$key]);
            file_put_contents($path, $keySet);

            $io->success('The file has been overwritten by the generated key.');

            return 0;
        }

        if (self::MODE_APPEND === $mode) {
            $keys = [];
            if (file_exists($path)) {
                $content = file_get_contents($path);
                $keys = $this->keySetSerializer->deserialize($content);
            }

            $keys[] = $key;
            $keySet = $this->keySetSerializer->serialize($keys);
            file_put_contents($path, $keySet);

            $io->success('The key has been added to the file.');

            return 0;
        }

        return 1;
    }
}

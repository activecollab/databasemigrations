<?php

/*
 * This file is part of the ActiveCollab DatabaseMigration project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseMigrations\Command;

use Doctrine\Common\Inflector\Inflector;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait Create
{
    use Base;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $old_umask = umask(0);

        try {
            $name = $this->getMigrationName($input);

            if (empty($name)) {
                throw new InvalidArgumentException('Migration name is required');
            }

            $migration_class = Inflector::classify(strtolower(str_replace([' ', '-'], ['_', '_'], $name)));
            $migration_class_path = $this->getMigrations()->getFinder()->prepareMigrationPath($migration_class, ...$this->getExtraArguments($input));

            if ($this->isDryRun($input)) {
                $output->writeln("Migration <comment>$migration_class</comment> will be create at <comment>$migration_class_path</comment> path.");
            } else {
                $migration_dir_path = dirname($migration_class_path);

                if (!is_dir($migration_dir_path)) {
                    if (mkdir($migration_dir_path, 0777, true)) {
                        $output->writeln("Directory <comment>$migration_dir_path</comment> created.");
                    } else {
                        throw new RuntimeException("Failed to create '$migration_dir_path' directory.");
                    }
                }

                if (is_file($migration_class_path)) {
                    throw new RuntimeException("Migration '$migration_class' already exists at '$migration_class_path' path");
                } else {
                    if (file_put_contents($migration_class_path, $this->getMigrationFileContents($migration_class))) {
                        $output->writeln("File <comment>$migration_class_path</comment> created.");
                    } else {
                        throw new RuntimeException("Failed to create '$migration_class_path' file");
                    }
                }
            }
        } catch (Exception $e) {
            throw $e;
        } finally {
            umask($old_umask);
        }

        return 0;
    }

    private function getMigrationFileContents(string $class_name): string
    {
        $namespace = $this->getNamespace();

        $contents = [
            '<?php',
            '',
        ];

        if ($header_comment = $this->getHeaderComment()) {
            $contents[] = '/*';
            $contents = array_merge($contents, array_map(function ($line) {
                if ($line) {
                    return ' * ' . $line;
                } else {
                    return ' *';
                }
            }, explode("\n", $header_comment)));
            $contents[] = ' */';
            $contents[] = '';
        }

        $contents[] = 'declare(strict_types=1);';
        $contents[] = '';

        if ($namespace) {
            $contents[] = 'namespace ' . $namespace . ';';
            $contents[] = '';
        }

        $contents[] = 'use ActiveCollab\DatabaseMigrations\Migration\Migration;';
        $contents[] = '';

        $contents[] = 'class ' . $class_name . ' extends Migration';
        $contents[] = '{';
        $contents[] = '    public function up(): void';
        $contents[] = '    {';
        $contents[] = '    }';
        $contents[] = '}';
        $contents[] = '';

        return implode("\n", $contents);
    }

    // ---------------------------------------------------
    //  Override
    // ---------------------------------------------------

    protected function getHeaderComment(): string
    {
        return '';
    }
    protected function getNamespace(): string
    {
        return '';
    }

    protected function isDryRun(InputInterface $input): bool
    {
        return false;
    }

    protected function getExtraArguments(InputInterface $input): array
    {
        return [];
    }

    abstract public function getMigrationName(InputInterface $input): string;
}

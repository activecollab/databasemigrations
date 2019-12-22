<?php

/*
 * This file is part of the ActiveCollab DatabaseMigration project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseMigrations\Finder;

use ActiveCollab\DateValue\DateTimeValue;
use ActiveCollab\FileSystem\Adapter\LocalAdapter;
use ActiveCollab\FileSystem\FileSystem;
use BadMethodCallException;
use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use RuntimeException;

class MigrationsInChangesetsFinder implements FinderInterface
{
    private $logger;
    private $namespace;
    private $migrations_dirs;

    public function __construct(LoggerInterface $logger, $namespace, string ...$migrations_dirs)
    {
        if (empty($migrations_dirs)) {
            throw new BadMethodCallException('Migration dir, or a list of migration dirs is required');
        }

        foreach ($migrations_dirs as $migration_dir) {
            if (!is_dir($migration_dir)) {
                throw new InvalidArgumentException("Directory '$migration_dir' does not exist");
            }
        }

        $this->logger = $logger;
        $this->namespace = $namespace ? '\\' . ltrim($namespace, '\\') : '';
        $this->migrations_dirs = $migrations_dirs;
    }

    public function getMigrationClassFilePathMap(): array
    {
        $migrations_by_changeset = [];
        $total_migrations_found = 0;

        foreach ($this->migrations_dirs as $migrations_dir) {
            $file_system = new FileSystem(new LocalAdapter($migrations_dir));

            foreach ($file_system->subdirs() as $changeset_dir) {
                if ($this->isValidChangesetName($changeset_dir)) {
                    $migrations_found = 0;

                    foreach ($file_system->files($changeset_dir, false) as $migration_file_path) {
                        if (empty($migrations_by_changeset[$changeset_dir])) {
                            $migrations_by_changeset[$changeset_dir] = [];
                        }

                        $migrations_by_changeset[$changeset_dir][$this->getMigrationClassName($migration_file_path)] = $file_system->getFullPath($migration_file_path);
                        ++$migrations_found;
                    }

                    if (empty($migrations_found)) {
                        $this->logger->debug(
                            'No migrations found in {migrations_dir}/{changeset}',
                            [
                                'migrations_dir' => $migrations_dir,
                                'changeset' => $changeset_dir
                            ]
                        );
                    } else {
                        $total_migrations_found += $migrations_found;
                    }
                } else {
                    throw new RuntimeException("Value '$changeset_dir' is not a valid changeset name");
                }
            }
        }

        $this->logger->debug(
            '{total_migrations} migrations found {total_changesets} changesets',
            [
                'total_migrations' => $total_migrations_found,
                'total_changesets' => count($migrations_by_changeset),
                'changesets' => array_keys($migrations_by_changeset)
            ]
        );

        ksort($migrations_by_changeset);

        $result = [];

        foreach ($migrations_by_changeset as $migrations) {
            $result = array_merge($result, $migrations);
        }

        return $result;
    }

    /**
     * Return true if $changeset_name is a valid changeset name.
     *
     * @param  string $changeset_name
     * @return bool
     */
    public function isValidChangesetName($changeset_name): bool
    {
        return (bool) preg_match('/^(\d{4})-(\d{2})-(\d{2})-(.*)$/', $changeset_name);
    }

    private function getMigrationClassName(string $migration_file_path): string
    {
        $migration_class = basename($migration_file_path, '.php');

        return $this->namespace ? $this->namespace . '\\' . $migration_class : $migration_class;
    }

    public function prepareMigrationPath(
        string $classified_name,
        string $migrations_dir = null,
        ...$extra_arguments
    ): string
    {
        if ($migrations_dir === null) {
            $migrations_dir = $this->migrations_dirs[0];
        } elseif (empty($migrations_dir)) {
            throw new InvalidArgumentException("Migrations dir is optional, but it can't be empty when specified");
        }

        if (!in_array($migrations_dir, $this->migrations_dirs)) {
            throw new InvalidArgumentException("Directory '$migrations_dir' is not managed by this finder");
        }

        $underscore_name = Inflector::tableize($classified_name);

        if (array_key_exists(0, $extra_arguments)) {
            if (trim($extra_arguments[0])) {
                $changeset = trim($extra_arguments[0]);
            } else {
                throw new InvalidArgumentException("Changeset argument is optional, but it can't be empty when specified");
            }
        } else {
            $changeset = $underscore_name;
        }

        $changeset = strtolower(str_replace([' ', '_'], ['-', '-'], $changeset));

        if (!$this->isValidChangesetName($changeset)) {
            $changeset = (new DateTimeValue())->format('Y-m-d') . '-' . $changeset;
        }

        return "$migrations_dir/$changeset/$classified_name.php";
    }
}

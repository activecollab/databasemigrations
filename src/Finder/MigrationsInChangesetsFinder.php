<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations\Finder;

use ActiveCollab\FileSystem\Adapter\LocalAdapter;
use ActiveCollab\FileSystem\FileSystem;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use BadMethodCallException;

/**
 * @package ActiveCollab\DatabaseMigrations\Finder
 */
class MigrationsInChangesetsFinder implements FinderInterface
{
    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string[]
     */
    private $migrations_dirs;

    /**
     * @param LoggerInterface $log
     * @param string          $namespace
     * @param string[]        ...$migrations_dirs
     */
    public function __construct(LoggerInterface &$log, $namespace, ...$migrations_dirs)
    {
        if (empty($migrations_dirs)) {
            throw new BadMethodCallException('Migration dir, or a list of migration dirs is required');
        }

        foreach ($migrations_dirs as $migration_dir) {
            if (!is_dir($migration_dir)) {
                throw new InvalidArgumentException("Directory '$migration_dir' does not exist");
            }
        }

        $this->log = $log;
        $this->namespace = $namespace ? '\\' . ltrim($namespace, '\\') : '';
        $this->migrations_dirs = $migrations_dirs;
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrationClassFilePathMap()
    {
        $migrations_by_changeset = [];

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
                        $migrations_found++;
                    }

                    if (empty($migrations_found)) {
                        $this->log->debug('No migrations found in {migrations_dir}/{changeset}', ['migrations_dir' => $migrations_dir, 'changeset' => $changeset_dir]);
                    }
                } else {
                    throw new RuntimeException("Value '$changeset_dir' is not a valid changeset name");
                }
            }
        }

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
    public function isValidChangesetName($changeset_name)
    {
        return (bool) preg_match('/^(\d{4})-(\d{2})-(\d{2})-(.*)$/', $changeset_name);
    }

    /**
     * Prepare full class name.
     *
     * @param  string $migration_file_path
     * @return string
     */
    private function getMigrationClassName($migration_file_path)
    {
        $migration_class = basename($migration_file_path, '.php');

        return $this->namespace ? $this->namespace . '\\' . $migration_class : $migration_class;
    }
}

<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseMigrations\Migration\MigrationInterface;
use Psr\Log\LoggerInterface;
use ActiveCollab\DatabaseMigrations\Finder\FinderInterface;
use RuntimeException;
use ReflectionClass;

/**
 * @package ActiveCollab\DatabaseMigrations
 */
class Migrations implements MigrationsInterface
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var FinderInterface
     */
    private $finder;

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @param ConnectionInterface $connection
     * @param FinderInterface     $finder
     * @param LoggerInterface     $log
     */
    public function __construct(ConnectionInterface &$connection, FinderInterface &$finder, LoggerInterface &$log)
    {
        $this->connection = $connection;
        $this->finder = $finder;
        $this->log = $log;
    }

    /**
     * @var bool
     */
    private $migrations_are_found = false;

    /**
     * @var MigrationInterface[]
     */
    private $migrations = [];

    /**
     * {@inheritdoc}
     */
    public function getMigrations()
    {
        if (!$this->migrations_are_found) {
            /** @var MigrationInterface[] $migrations_by_class */
            $migrations_by_class = [];

            foreach ($this->finder->getMigrationClassFilePathMap() as $migration_class => $migration_file_path) {
                if (is_file($migration_file_path)) {
                    if (class_exists($migration_class, false)) {
                        $reflection = new ReflectionClass($migration_class);

                        if ($reflection->implementsInterface(MigrationInterface::class) && !$reflection->isAbstract()) {
                            $migrations_by_class[$migration_class] = new $migration_class($this->connection);
                        }
                    } else {
                        throw new RuntimeException("Migration class '$migration_class' not found");
                    }
                } else {
                    throw new RuntimeException("File '$migration_file_path' not found");
                }
            }

            foreach ($migrations_by_class as $migration_class => $migration) {
                if (empty($this->migrations[$migration_class])) {
                    foreach ($migration->getExecuteAfter() as $migration_execute_after) {
                        if (empty($this->migrations[$migration_execute_after])) {
                            if (isset($migrations_by_class[$migration_execute_after])) {
                                $this->migrations[$migration_execute_after] = $migrations_by_class[$migration_execute_after];
                            } else {
                                throw new RuntimeException("Migration '$migration_execute_after' not found");
                            }
                        }
                    }

                    $this->migrations[$migration_class] = $migration;
                }
            }

            $this->migrations = array_values($this->migrations); // Reindex and remove class name as key. 0..n will work.
            $this->migrations_are_found = true;
        }

        return $this->migrations;
    }

    /**
     * {@inheritdoc}
     */
    public function up()
    {

    }
}

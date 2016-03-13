<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseMigrations\Finder\FinderInterface;
use ActiveCollab\DatabaseMigrations\Migration\MigrationInterface;
use ActiveCollab\DateValue\DateTimeValue;
use InvalidArgumentException;
use LogicException;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use RuntimeException;

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
     * @var string
     */
    private $table_name;

    /**
     * @param ConnectionInterface $connection
     * @param FinderInterface     $finder
     * @param LoggerInterface     $log
     * @param string              $table_name
     */
    public function __construct(ConnectionInterface &$connection, FinderInterface &$finder, LoggerInterface &$log, $table_name = 'executed_database_migrations')
    {
        if (empty($table_name)) {
            throw new InvalidArgumentException('Table name is required');
        }

        $this->connection = $connection;
        $this->finder = $finder;
        $this->log = $log;
        $this->table_name = $table_name;
    }

    /**
     * {@inheritdoc}
     */
    public function &getFinder()
    {
        return $this->finder;
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
            $migration_class_file_path_map = $this->finder->getMigrationClassFilePathMap();

            $migrations_by_class = $this->getMigrationInstances($migration_class_file_path_map);

            foreach ($migrations_by_class as $migration_class => $migration) {
                if (empty($this->migrations[$migration_class])) {
                    foreach ($migration->getExecuteAfter() as $execute_after_migration_file_path) {
                        $execute_after_migration_class = $this->getMigrationClassByMigrationPath($execute_after_migration_file_path, $migration_class_file_path_map);

                        if (empty($this->migrations[$execute_after_migration_class])) {
                            if (isset($migrations_by_class[$execute_after_migration_class])) {
                                $this->migrations[$execute_after_migration_class] = $migrations_by_class[$execute_after_migration_class];
                            } else {
                                throw new RuntimeException("Migration '$execute_after_migration_class' not found");
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
     * Return an array of MigrationInterface instances indexed by class name.
     *
     * @param  array                $migration_class_file_path_map
     * @return MigrationInterface[]
     */
    private function getMigrationInstances(array $migration_class_file_path_map)
    {
        $result = [];

        foreach ($migration_class_file_path_map as $migration_class => $migration_file_path) {
            if (is_file($migration_file_path)) {
                require_once $migration_file_path;

                if (class_exists($migration_class, false)) {
                    $reflection = new ReflectionClass($migration_class);

                    if ($reflection->implementsInterface(MigrationInterface::class) && !$reflection->isAbstract()) {
                        $result[$migration_class] = new $migration_class($this->connection);
                    }
                } else {
                    throw new RuntimeException("Migration class '$migration_class' not found");
                }
            } else {
                throw new RuntimeException("File '$migration_file_path' not found");
            }
        }

        return $result;
    }

    /**
     * Return migration class name based on migration's path.
     *
     * @param  string $migration_file_path
     * @param  array  $migration_class_file_path_map
     * @return string
     */
    private function getMigrationClassByMigrationPath($migration_file_path, array $migration_class_file_path_map)
    {
        if ($migration_class = array_search($migration_file_path, $migration_class_file_path_map)) {
            return $migration_class;
        } else {
            throw new RuntimeException("Migration from '$migration_file_path' not loaded");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function up(callable $output = null)
    {
        foreach ($this->getMigrations() as $migration) {
            $migration_class = get_class($migration);

            if ($this->isExecuted($migration)) {
                $this->log->debug('Migration {migration} already executed', ['migration' => $migration_class]);

                if ($output) {
                    $output("Migration <comment>$migration_class</comment> is already executed");
                }
            } else {
                $this->log->debug('Ready to execute {migration} migration', ['migration' => $migration_class]);

                if ($output) {
                    $output("Ready to execute <comment>$migration_class</comment> migration");
                }

                $reference_time = microtime(true);
                $this->execute($migration);
                $exec_time = number_format(microtime(true) - $reference_time, 5, '.', '');

                $this->log->debug('Migration {migration} executed', ['migration' => $migration_class, 'exec_time' => (float) $exec_time]);

                if ($output) {
                    $output("Migration <comment>$migration_class</comment> is executed in <comment>$exec_time seconds</comment>");
                }
            }
        }
    }

    /**
     * @var bool|null
     */
    private $table_exists = null;

    /**
     * Return name of the table where we store info about executed migrations.
     *
     * @return string
     */
    public function getTableName()
    {
        if ($this->table_exists === null && !in_array($this->table_name, $this->connection->getTableNames())) {
            $this->connection->execute('CREATE TABLE ' . $this->connection->escapeTableName($this->table_name) . ' (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                `executed_at` datetime NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `migration` (`migration`),
                KEY `executed_on` (`executed_at`)
            ) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');

            $this->table_exists = true;
        }

        return $this->table_name;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(MigrationInterface $migration)
    {
        if ($this->isExecuted($migration)) {
            throw new LogicException('Migration ' . get_class($migration) . ' is already executed');
        }

        $this->connection->transact(function () use ($migration) {
            $migration->up();

            $this->connection->insert($this->getTableName(), [
                'migration' => get_class($migration),
                'executed_at' => new DateTimeValue(),
            ]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function isExecuted(MigrationInterface $migration)
    {
        return (bool) $this->connection->count($this->getTableName(), ['`migration` = ?', get_class($migration)]);
    }

    /**
     * {@inheritdoc}
     */
    public function setAllAsExecuted()
    {
        $this->connection->transact(function () {
            $timestamp = new DateTimeValue();

            $batch = $this->connection->batchInsert($this->getTableName(), ['migration', 'executed_at'], 50, ConnectionInterface::REPLACE);

            foreach ($this->getMigrations() as $migration) {
                $batch->insert(get_class($migration), $timestamp);
            }

            $batch->done();
        });
    }
}

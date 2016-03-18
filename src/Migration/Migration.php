<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations\Migration;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use Psr\Log\LoggerInterface;

/**
 * @package ActiveCollab\DatabaseMigrations\Migration
 */
abstract class Migration implements MigrationInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var LoggerInterface
     */
    protected $log;

    /**
     * @param ConnectionInterface $connection
     * @param LoggerInterface|null $log
     */
    public function __construct(ConnectionInterface &$connection, LoggerInterface &$log)
    {
        $this->connection = $connection;
        $this->log = $log;

        $this->configure();
    }

    /**
     * Configure migration, after it has been constructed.
     */
    protected function configure()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function canExecute(&$reason = null)
    {
        return true;
    }

    /**
     * @var string[]
     */
    private $execute_after = [];

    /**
     * {@inheritdoc}
     */
    public function getExecuteAfter()
    {
        return $this->execute_after;
    }

    /**
     * Make sure that this migration is executed after given list of migrations.
     *
     * @param array ...$migration_paths
     */
    protected function executeAfter(...$migration_paths)
    {
        $this->execute_after = array_merge($this->execute_after, $migration_paths);
    }
}

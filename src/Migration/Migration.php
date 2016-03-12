<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations\Migration;

use ActiveCollab\DatabaseConnection\ConnectionInterface;

/**
 * @package ActiveCollab\DatabaseMigrations\Migration
 */
abstract class Migration implements MigrationInterface
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface &$connection)
    {
        $this->connection = $connection;

        $this->configure();
    }

    /**
     * Configure migration, after it has been constructed.
     */
    protected function configure()
    {
    }

    /**
     * @var string[]
     */
    private $execute_after = [];

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

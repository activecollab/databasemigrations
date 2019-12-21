<?php

/*
 * This file is part of the ActiveCollab DatabaseMigration project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseMigrations\Migration;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use Psr\Log\LoggerInterface;

abstract class Migration implements MigrationInterface
{
    protected $connection;
    protected $logger;

    public function __construct(ConnectionInterface $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;

        $this->configure();
    }

    protected function configure(): void
    {
    }

    public function canExecute(string &$reason = null): bool
    {
        return true;
    }

    private $execute_after = [];

    public function getExecuteAfter(): array
    {
        return $this->execute_after;
    }

    protected function executeAfter(string ...$migration_paths): void
    {
        $this->execute_after = array_merge($this->execute_after, $migration_paths);
    }
}

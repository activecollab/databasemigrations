<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use Psr\Log\LoggerInterface;

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
}

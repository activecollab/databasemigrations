<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations;

use Psr\Log\LoggerInterface;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseMigrations
 */
class MigrationsInChangesetsFinder implements FinderInterface
{
    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var string[]
     */
    private $migration_dirs;

    /**
     * @param LoggerInterface $log
     * @param string[]        ...$migration_dirs
     */
    public function __construct(LoggerInterface &$log, ...$migration_dirs)
    {
        if (empty($migration_dirs)) {
            throw new InvalidArgumentException('Migration dir, or a list of migration dirs is required');
        }

        foreach ($migration_dirs as $migration_dir) {
            if (!is_dir($migration_dir)) {
                throw new InvalidArgumentException("Directory '$migration_dir' does not exist");
            }
        }

        $this->log = $log;
        $this->migration_dirs = $migration_dirs;
    }
}

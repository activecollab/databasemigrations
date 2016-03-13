<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations;

use ActiveCollab\DatabaseMigrations\Migration\MigrationInterface;
use ActiveCollab\DateValue\DateTimeValueInterface;

/**
 * @package ActiveCollab\DatabaseMigrations
 */
interface MigrationsInterface
{
    /**
     * Find and return all migrations (using Finder object).
     *
     * @return MigrationInterface[]
     */
    public function getMigrations();

    /**
     * Migrate up.
     *
     * @param callable|null $output
     */
    public function up(callable $output = null);

    /**
     * Set all found migration as executed. Useful during app's installation, to mark existing migrations as migrated.
     *
     * If $timestamp is NULL, current timestamp will be used.
     *
     * @param DateTimeValueInterface|null $timestamp
     */
    public function setAllAsExecuted(DateTimeValueInterface $timestamp = null);
}

<?php

/*
 * This file is part of the ActiveCollab DatabaseMigration project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations;

use ActiveCollab\DatabaseMigrations\Finder\FinderInterface;
use ActiveCollab\DatabaseMigrations\Migration\MigrationInterface;

/**
 * @package ActiveCollab\DatabaseMigrations
 */
interface MigrationsInterface
{
    /**
     * @return FinderInterface
     */
    public function getFinder();

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
     * Execute an individual migraion.
     *
     * @param MigrationInterface $migration
     */
    public function execute(MigrationInterface $migration);

    /**
     * Return true if $migration is executed.
     *
     * @param  MigrationInterface $migration
     * @return bool
     */
    public function isExecuted(MigrationInterface $migration);

    /**
     * Set all found migration as executed. Useful during app's installation, to mark existing migrations as migrated.
     */
    public function setAllAsExecuted();
}

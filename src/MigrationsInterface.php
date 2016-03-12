<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations;

use ActiveCollab\DatabaseMigrations\Migration\MigrationInterface;

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
     */
    public function up();
}

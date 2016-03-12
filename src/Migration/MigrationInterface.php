<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations\Migration;

/**
 * @package ActiveCollab\DatabaseMigrations\Migration
 */
interface MigrationInterface
{
    /**
     * Migrate up.
     */
    public function up();

    /**
     * Rollback changes made by this migration.
     */
    public function down();

    /**
     * Return array of migrations that need to be executed before this migration can be executed.
     *
     * @return array
     */
    public function getExecuteAfter();
}

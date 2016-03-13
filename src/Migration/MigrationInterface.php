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
     * Return true if all pre-conditions are met for this migration to run.
     *
     * @param  string|null $reason
     * @return bool
     */
    public function canExecute(&$reason = null);

    /**
     * Return array of migrations that need to be executed before this migration can be executed.
     *
     * @return array
     */
    public function getExecuteAfter();

    /**
     * Migrate up.
     */
    public function up();
}

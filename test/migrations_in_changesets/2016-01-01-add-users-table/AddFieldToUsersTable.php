<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations;

use ActiveCollab\DatabaseMigrations\Migration\Migration;

/**
 * @package ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations
 */
class AddFieldToUsersTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->executeAfter(__DIR__ . '/AddUsersTable.php');
    }

    /**
     * {@inheritdoc}
     */
    public function up()
    {
    }
}

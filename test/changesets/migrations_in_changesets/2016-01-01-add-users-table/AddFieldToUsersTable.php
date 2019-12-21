<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations;

use ActiveCollab\DatabaseMigrations\Migration\Migration;

class AddFieldToUsersTable extends Migration
{
    protected function configure(): void
    {
        $this->executeAfter(__DIR__ . '/AddUsersTable.php');
    }

    public function up(): void
    {
    }
}

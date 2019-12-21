<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations;

use ActiveCollab\DatabaseMigrations\Migration\Migration;

class AddIndexToTheNewField extends Migration
{
    protected function configure(): void
    {
        $this->executeAfter(__DIR__ . '/AddFieldToUsersTable.php');
    }

    public function up(): void
    {
    }
}

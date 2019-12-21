<?php

/*
 * This file is part of the ActiveCollab DatabaseMigration project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseMigrations\Migration;

interface MigrationInterface
{
    public function canExecute(string &$reason = null): bool;
    public function getExecuteAfter(): array;
    public function up(): void;
}

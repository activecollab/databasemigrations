<?php

/*
 * This file is part of the ActiveCollab DatabaseMigration project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseMigrations;

use ActiveCollab\DatabaseMigrations\Finder\FinderInterface;
use ActiveCollab\DatabaseMigrations\Migration\MigrationInterface;

interface MigrationsInterface
{
    public function getFinder(): FinderInterface;
    public function getMigrations(): array;

    public function up(callable $output = null): void;

    public function execute(MigrationInterface $migration): void;
    public function isExecuted(MigrationInterface $migration): bool;

    public function setAllAsExecuted(): void;
}

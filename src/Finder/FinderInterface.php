<?php

/*
 * This file is part of the ActiveCollab DatabaseMigration project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseMigrations\Finder;

interface FinderInterface
{
    public function getMigrationClassFilePathMap(): array;
    public function prepareMigrationPath(
        string $classified_name,
        string $migrations_dir = null,
        ...$extra_arguments
    ): string;
}

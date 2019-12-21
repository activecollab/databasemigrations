<?php

/*
 * This file is part of the ActiveCollab DatabaseMigration project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseMigrations\Command;

use ActiveCollab\DatabaseMigrations\MigrationsInterface;

trait Base
{
    abstract protected function getMigrations(): MigrationsInterface;
}

<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations\Command;

use ActiveCollab\DatabaseMigrations\MigrationsInterface;

/**
 * @package ActiveCollab\DatabaseMigrations\Command
 */
trait Base
{
    /**
     * @return MigrationsInterface
     */
    abstract protected function &getMigrations();
}

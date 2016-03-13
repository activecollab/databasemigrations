<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations\Test;

use ActiveCollab\DatabaseMigrations\Finder\MigrationsInChangesetsFinder;

/**
 * @package ActiveCollab\DatabaseMigrations\Test
 */
class ChangesetNameTest extends TestCase
{
    /**
     * Test changeset name validation.
     */
    public function testFindMigrations()
    {
        $finder = new MigrationsInChangesetsFinder($this->log, '', $this->migrations_path);

        $this->assertFalse($finder->isValidChangesetName('2016-01-01'));
        $this->assertFalse($finder->isValidChangesetName('fix-users-table'));
        $this->assertTrue($finder->isValidChangesetName('2016-01-01-fix-users-table'));
    }
}

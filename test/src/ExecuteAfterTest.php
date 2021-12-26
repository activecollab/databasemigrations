<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations\Test;

use ActiveCollab\DatabaseMigrations\Finder\MigrationsInChangesetsFinder;
use ActiveCollab\DatabaseMigrations\Migrations;

/**
 * @package ActiveCollab\DatabaseMigrations\Test
 */
class ExecuteAfterTest extends TestCase
{
    /**
     * Test if migrations are found and sorted correctly based on executeAfter() info.
     */
    public function testFindFilePaths()
    {
        $finder = new MigrationsInChangesetsFinder($this->log, 'ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations', $this->migrations_path);
        $migrations = new Migrations($this->connection, $finder, $this->log);

        $migrations = $migrations->getMigrations();

        $this->assertIsArray($migrations);
        $this->assertCount(4, $migrations);

        $this->assertInstanceOf('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddUsersTable', $migrations[0]);
        $this->assertInstanceOf('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddFieldToUsersTable', $migrations[1]);
        $this->assertInstanceOf('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddIndexToTheNewField', $migrations[2]);
        $this->assertInstanceOf('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddIndexesToUsersTable', $migrations[3]);
    }
}

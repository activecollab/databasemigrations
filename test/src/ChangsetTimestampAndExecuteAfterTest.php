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
class ChangsetTimestampAndExecuteAfterTest extends TestCase
{
    /**
     * Test if migrations are found and sorted correctly based on executeAfter() info.
     */
    public function testFindFilePaths()
    {
        $migrations = new Migrations($this->connection, new MigrationsInChangesetsFinder($this->log, 'ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations', $this->migrations_path, dirname(__DIR__) . '/changesets/inject_in_migrations_with_changeset'), $this->log);

        $migrations = $migrations->getMigrations();

        $this->assertInternalType('array', $migrations);
        $this->assertCount(5, $migrations);

        $this->assertInstanceOf('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddUsersTable', $migrations[0]);
        $this->assertInstanceOf('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddFieldToUsersTable', $migrations[1]);
        $this->assertInstanceOf('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddIndexToTheNewField', $migrations[2]);
        $this->assertInstanceOf('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddUserRolesTable', $migrations[3]);
        $this->assertInstanceOf('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddIndexesToUsersTable', $migrations[4]);
    }
}

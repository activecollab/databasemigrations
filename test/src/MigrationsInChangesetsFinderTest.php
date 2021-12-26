<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations\Test;

use ActiveCollab\DatabaseMigrations\Finder\MigrationsInChangesetsFinder;
use BadMethodCallException;
use InvalidArgumentException;
use RuntimeException;

/**
 * @package ActiveCollab\DatabaseMigrations\Test
 */
class MigrationsInChangesetsFinderTest extends TestCase
{
    public function testErrorWhenNoMigrationDirsSet()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage("Migration dir, or a list of migration dirs is required");

        new MigrationsInChangesetsFinder($this->log, '');
    }

    public function testErrorOnInvalidMigrationsDir()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Directory 'not a valid migrations dir' does not exist");

        new MigrationsInChangesetsFinder($this->log, '', 'not a valid migrations dir');
    }

    public function testErrorOnInvalidChangsetName()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Value 'invalid_changeset_name' is not a valid changeset name");

        (new MigrationsInChangesetsFinder($this->log, '', dirname(__DIR__) . '/changesets/migrations_with_invalid_changeset_name'))->getMigrationClassFilePathMap();
    }

    /**
     * Test if full migration file paths are found.
     */
    public function testFindFilePaths()
    {
        $finder = new MigrationsInChangesetsFinder($this->log, '', $this->migrations_path);

        $map = $finder->getMigrationClassFilePathMap();

        $this->assertIsArray($map);
        $this->assertCount(4, $map);

        $this->assertContains("$this->migrations_path/2016-01-01-add-users-table/AddUsersTable.php", $map);
        $this->assertContains("$this->migrations_path/2016-01-01-add-users-table/AddFieldToUsersTable.php", $map);
        $this->assertContains("$this->migrations_path/2016-01-01-add-users-table/AddIndexToTheNewField.php", $map);
        $this->assertContains("$this->migrations_path/2016-02-01-fix-user-table-indexes/AddIndexesToUsersTable.php", $map);
    }

    /**
     * Test find migration classes in global namespace.
     */
    public function testFindGlobal()
    {
        $finder = new MigrationsInChangesetsFinder($this->log, '', $this->migrations_path);

        $map = $finder->getMigrationClassFilePathMap();

        $this->assertIsArray($map);
        $this->assertCount(4, $map);

        $this->assertArrayHasKey('AddUsersTable', $map);
        $this->assertArrayHasKey('AddFieldToUsersTable', $map);
        $this->assertArrayHasKey('AddIndexToTheNewField', $map);
        $this->assertArrayHasKey('AddIndexesToUsersTable', $map);
    }

    /**
     * Test find namespaced migration classes.
     */
    public function testFindNamespaced()
    {
        $finder = new MigrationsInChangesetsFinder($this->log, 'ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations', $this->migrations_path);

        $map = $finder->getMigrationClassFilePathMap();

        $this->assertIsArray($map);
        $this->assertCount(4, $map);

        $this->assertArrayHasKey('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddUsersTable', $map);
        $this->assertArrayHasKey('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddFieldToUsersTable', $map);
        $this->assertArrayHasKey('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddIndexToTheNewField', $map);
        $this->assertArrayHasKey('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddIndexesToUsersTable', $map);
    }

    /**
     * Test if we can successfully load info from multiple migration dirs.
     */
    public function testMultipleMigrationDirs()
    {
        $finder = new MigrationsInChangesetsFinder($this->log, 'ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations', $this->migrations_path, dirname(__DIR__) . '/changesets/inject_in_migrations_with_changeset');

        $map = $finder->getMigrationClassFilePathMap();

        $this->assertIsArray($map);
        $this->assertCount(5, $map);

        $this->assertArrayHasKey('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddUsersTable', $map);
        $this->assertArrayHasKey('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddFieldToUsersTable', $map);
        $this->assertArrayHasKey('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddIndexToTheNewField', $map);
        $this->assertArrayHasKey('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddIndexesToUsersTable', $map);
        $this->assertArrayHasKey('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddUserRolesTable', $map);
    }
}

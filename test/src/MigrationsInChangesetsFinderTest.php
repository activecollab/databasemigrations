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
class MigrationsInChangesetsFinderTest extends TestCase
{
    /**
     * @var string
     */
    private $migrations_path;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->migrations_path = dirname(__DIR__) . '/migrations_in_changesets';
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Migration dir, or a list of migration dirs is required
     */
    public function testErrorWhenNoMigrationDirsSet()
    {
        new MigrationsInChangesetsFinder($this->log, '');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Directory 'not a valid migrations dir' does not exist
     */
    public function testErrorOnInvalidMigrationsDir()
    {
        new MigrationsInChangesetsFinder($this->log, '', 'not a valid migrations dir');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Value 'invalid_changeset_name' is not a valid changeset name
     */
    public function testErrorOnInvalidChangsetName()
    {
        (new MigrationsInChangesetsFinder($this->log, '', dirname(__DIR__) . '/migrations_with_invalid_changeset_name'))->getMigrationClassFilePathMap();
    }

    /**
     * Test if full migration file paths are found.
     */
    public function testFindFilePaths()
    {
        $finder = new MigrationsInChangesetsFinder($this->log, '', $this->migrations_path);

        $map = $finder->getMigrationClassFilePathMap();

        $this->assertInternalType('array', $map);
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

        $this->assertInternalType('array', $map);
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

        $this->assertInternalType('array', $map);
        $this->assertCount(4, $map);

        $this->assertArrayHasKey('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddUsersTable', $map);
        $this->assertArrayHasKey('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddFieldToUsersTable', $map);
        $this->assertArrayHasKey('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddIndexToTheNewField', $map);
        $this->assertArrayHasKey('\ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddIndexesToUsersTable', $map);
    }
}
